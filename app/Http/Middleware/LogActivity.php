<?php

namespace App\Http\Middleware;

use App\Support\ActivityAnnotator;
use App\Support\ActivityDescriber;
use App\Support\ActivityPayloadMasker;
use App\Support\ActivityRequestFilter;
use App\Support\ActivityTypeClassifier;
use App\Support\GeoLocationResolver;
use App\Support\UserAgentParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Writes one activity_logs row per captured request, after the response has
 * been sent.
 *
 * All per-request state lives on $request->attributes rather than on $this,
 * because Kernel::terminateMiddleware() resolves a fresh instance of this class
 * and would otherwise see none of it. That same attribute bag is the channel
 * auth listeners and the exception handler use to contribute to the row.
 */
class LogActivity
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (config('activity-log.enabled') && ! ActivityRequestFilter::shouldSkip($request)) {
                $visitorId = $this->resolveVisitorId($request);

                if (! ActivityRequestFilter::isDuplicate($request, $visitorId)) {
                    $request->attributes->set(ActivityAnnotator::KEY_CAPTURE, true);
                    $request->attributes->set(ActivityAnnotator::KEY_VISITOR, $visitorId);
                    $request->attributes->set(
                        ActivityAnnotator::KEY_SESSION,
                        $request->hasSession() ? sha1($request->session()->getId()) : null
                    );

                    if ($this->shouldCapturePayload($request)) {
                        $request->attributes->set(
                            ActivityAnnotator::KEY_PAYLOAD,
                            ActivityPayloadMasker::mask($request->all())
                        );
                    }
                }
            }
        } catch (Throwable $e) {
            $this->logFailure('Failed to prepare activity capture.', $e);
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            if (! $request->attributes->get(ActivityAnnotator::KEY_CAPTURE, false)) {
                return;
            }

            $statusCode = $response->getStatusCode();

            if (ActivityRequestFilter::statusExcluded($statusCode)) {
                return;
            }

            DB::connection(config('activity-log.connection'))
                ->table(config('activity-log.table'))
                ->insert($this->buildRow($request, $response, $statusCode));
        } catch (Throwable $e) {
            $this->logFailure('Failed to write activity log row.', $e);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRow(Request $request, Response $response, int $statusCode): array
    {
        $type = ActivityTypeClassifier::classify($request, $response);
        $meta = (array) $request->attributes->get(ActivityAnnotator::KEY_META, []);
        $actor = $this->resolveActor($request);
        $subject = (array) $request->attributes->get(ActivityAnnotator::KEY_SUBJECT, []);

        $userAgent = (string) $request->userAgent();
        $device = $userAgent !== '' ? UserAgentParser::parse($userAgent) : [];

        $payload = $request->attributes->get(ActivityAnnotator::KEY_PAYLOAD);
        $query = config('activity-log.capture.query_params', true) && $request->query() !== []
            ? ActivityPayloadMasker::mask($request->query())
            : null;

        $description = $request->attributes->get(ActivityAnnotator::KEY_DESCRIPTION)
            ?? ActivityDescriber::describe($type, $request, $meta);

        return array_merge([
            'user_id' => $actor['user_id'] ?? null,
            'user_name' => $actor['user_name'] ?? null,
            'user_email' => $actor['user_email'] ?? null,
            'role_id' => $actor['role_id'] ?? null,
            'role_name' => $actor['role_name'] ?? null,
            'is_authenticated' => ($actor['user_id'] ?? null) !== null,
            'visitor_id' => $request->attributes->get(ActivityAnnotator::KEY_VISITOR),
            'session_id' => $request->attributes->get(ActivityAnnotator::KEY_SESSION),
            'user_session_id' => $request->hasSession() ? $request->session()->get('user_activity_session_id') : null,

            'occurred_at' => now(),
            'duration_ms' => $this->durationMs(),

            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => Str::limit($request->path(), 188, ''),
            'route_name' => $request->route()?->getName(),
            'controller_action' => $this->controllerAction($request),
            'referer' => config('activity-log.capture.referer', true)
                ? Str::limit((string) $request->headers->get('referer'), 508, '') ?: null
                : null,
            'is_ajax' => $request->ajax(),
            'query_params' => ActivityPayloadMasker::encode($query),
            'payload' => ActivityPayloadMasker::encode(is_array($payload) ? $payload : null),
            'status_code' => $statusCode,

            'ip_address' => $request->ip(),
            'user_agent' => config('activity-log.capture.user_agent', true) ? ($userAgent ?: null) : null,
            'browser' => $device['browser'] ?? null,
            'platform' => $device['platform'] ?? null,
            'device_type' => $device['device_type'] ?? null,

            'activity_type' => $type->value,
            'description' => $description,
            'label' => ActivityDescriber::label($request),
            'module' => ActivityDescriber::module($request),
            'subject_type' => $subject['type'] ?? null,
            'subject_id' => $subject['id'] ?? null,
            'meta' => ActivityPayloadMasker::encode($meta === [] ? null : $meta),

            'created_at' => now(),
        ], $this->geoColumns($request));
    }

    /**
     * Inline resolution keeps geolocation working without a scheduler. In
     * deferred mode the row is written with null geo and backfilled by the
     * activity-log:resolve-geo command.
     *
     * @return array<string, mixed>
     */
    private function geoColumns(Request $request): array
    {
        $ip = $request->ip();

        if ($ip === null
            || ! config('activity-log.geo.enabled')
            || config('activity-log.geo.mode') !== 'inline') {
            return [];
        }

        $location = app(GeoLocationResolver::class)->resolve($ip);

        if ($location === null) {
            return [];
        }

        return array_merge($location->toColumns(), ['geo_resolved_at' => now()]);
    }

    /**
     * Prefers the actor annotated by an auth listener over the current guard,
     * because on a logout request the guard is already empty.
     *
     * @return array<string, mixed>
     */
    private function resolveActor(Request $request): array
    {
        $annotated = $request->attributes->get(ActivityAnnotator::KEY_ACTOR);

        if (is_array($annotated)) {
            return $annotated;
        }

        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $name = trim(implode(' ', array_filter([$user->first_name, $user->last_name])));

        return [
            'user_id' => $user->id,
            'user_name' => $name !== '' ? $name : $user->username,
            'user_email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role?->role_name,
        ];
    }

    /**
     * Reads LARAVEL_START directly rather than stashing a handle() timestamp:
     * simpler, and it covers bootstrap and response send too, which is what the
     * user actually waited for.
     */
    private function durationMs(): ?int
    {
        if (! defined('LARAVEL_START')) {
            return null;
        }

        return (int) round((microtime(true) - LARAVEL_START) * 1000);
    }

    private function controllerAction(Request $request): ?string
    {
        $action = $request->route()?->getActionName();

        return $action && $action !== 'Closure' ? Str::limit($action, 188, '') : null;
    }

    /**
     * Snapshotted before the controller runs: controllers in this application
     * call $request->merge(), and UploadedFile handles may already be consumed
     * afterwards.
     */
    private function shouldCapturePayload(Request $request): bool
    {
        return config('activity-log.capture.payload', true)
            && in_array(
                $request->method(),
                (array) config('activity-log.capture.payload_methods', []),
                true
            );
    }

    /**
     * The visitor cookie is the primary timeline key. Session IDs change on
     * login and logout, so only this survives a full journey.
     */
    private function resolveVisitorId(Request $request): string
    {
        $name = (string) config('activity-log.visitor.cookie');
        $existing = $request->cookie($name);

        if (is_string($existing) && Str::isUuid($existing)) {
            return $existing;
        }

        $visitorId = (string) Str::uuid();

        Cookie::queue(Cookie::make(
            name: $name,
            value: $visitorId,
            minutes: (int) config('activity-log.visitor.lifetime_minutes'),
            path: '/',
            domain: null,
            secure: (bool) config('session.secure', false),
            httpOnly: (bool) config('activity-log.visitor.http_only', true),
            raw: false,
            sameSite: (string) config('activity-log.visitor.same_site', SymfonyCookie::SAMESITE_LAX),
        ));

        return $visitorId;
    }

    private function logFailure(string $message, Throwable $e): void
    {
        Log::channel(config('activity-log.log_channel'))->error($message, ['exception' => $e]);
    }
}
