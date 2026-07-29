<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Decides whether a request is worth an audit row.
 *
 * The exclusion list is the single biggest lever on activity_logs growth, so it
 * is entirely config driven rather than hardcoded.
 */
final class ActivityRequestFilter
{
    public static function shouldSkip(Request $request): bool
    {
        if (self::methodExcluded($request)) {
            return true;
        }

        if (self::pathExcluded($request)) {
            return true;
        }

        if (self::routeExcluded($request)) {
            return true;
        }

        if (self::ipExcluded($request)) {
            return true;
        }

        if (self::botExcluded($request)) {
            return true;
        }

        if (! auth()->check() && ! config('activity-log.capture.guests', true)) {
            return true;
        }

        if ($request->ajax() && ! config('activity-log.capture.ajax', true)) {
            return true;
        }

        return false;
    }

    /**
     * Suppresses the duplicate rows produced by rapid re-navigation to the same
     * page. Mirrors the dedupe window used by the legacy TrackPageVisit
     * middleware. Costs one cache read per GET; set duplicate_seconds to 0 to
     * remove it entirely.
     */
    public static function isDuplicate(Request $request, ?string $visitorId): bool
    {
        $seconds = (int) config('activity-log.throttle.duplicate_seconds', 0);

        if ($seconds <= 0 || $visitorId === null) {
            return false;
        }

        $methods = (array) config('activity-log.throttle.duplicate_methods', ['GET']);

        if (! in_array($request->method(), $methods, true)) {
            return false;
        }

        $key = 'activity-log:dedupe:'.$visitorId.':'.$request->method().':'.sha1($request->path());

        if (Cache::has($key)) {
            return true;
        }

        Cache::put($key, 1, $seconds);

        return false;
    }

    /**
     * Applied after the response exists, so status based exclusions can be
     * configured without giving up the rest of the pipeline.
     */
    public static function statusExcluded(?int $statusCode): bool
    {
        if ($statusCode === null) {
            return false;
        }

        return in_array($statusCode, (array) config('activity-log.exclude.status_codes', []), true);
    }

    private static function methodExcluded(Request $request): bool
    {
        return in_array(
            $request->method(),
            (array) config('activity-log.exclude.methods', []),
            true
        );
    }

    private static function pathExcluded(Request $request): bool
    {
        $paths = (array) config('activity-log.exclude.paths', []);

        if ($paths !== [] && $request->is(...$paths)) {
            return true;
        }

        $path = $request->path();

        foreach ((array) config('activity-log.exclude.extensions', []) as $extension) {
            if (Str::endsWith($path, '.'.$extension)) {
                return true;
            }
        }

        return false;
    }

    private static function routeExcluded(Request $request): bool
    {
        $names = (array) config('activity-log.exclude.route_names', []);

        return $names !== [] && $request->routeIs(...$names);
    }

    private static function ipExcluded(Request $request): bool
    {
        $ip = $request->ip();

        return $ip !== null && in_array($ip, (array) config('activity-log.exclude.ips', []), true);
    }

    private static function botExcluded(Request $request): bool
    {
        $userAgent = (string) $request->userAgent();

        if ($userAgent === '') {
            return false;
        }

        foreach ((array) config('activity-log.exclude.user_agent_patterns', []) as $pattern) {
            if (preg_match($pattern, $userAgent) === 1) {
                return true;
            }
        }

        return false;
    }
}
