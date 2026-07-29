<?php

namespace App\Support;

use App\Enums\ActivityLogType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Throwable;

/**
 * Lets event listeners and the exception handler contribute to the single audit
 * row that LogActivity writes in terminate(), instead of writing rows of their
 * own.
 *
 * State lives on the Request because Kernel::terminateMiddleware() resolves a
 * fresh middleware instance, so anything set on $this during handle() is gone
 * by terminate(). The same Request object is passed to both.
 */
final class ActivityAnnotator
{
    public const KEY_CAPTURE = 'activity_log.capture';

    public const KEY_TYPE = 'activity_log.type';

    public const KEY_META = 'activity_log.meta';

    public const KEY_ACTOR = 'activity_log.actor';

    public const KEY_VISITOR = 'activity_log.visitor_id';

    public const KEY_SESSION = 'activity_log.session_id';

    public const KEY_PAYLOAD = 'activity_log.payload';

    public const KEY_DESCRIPTION = 'activity_log.description';

    public const KEY_SUBJECT = 'activity_log.subject';

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function type(ActivityLogType $type, array $meta = []): void
    {
        if (! self::available()) {
            return;
        }

        $request = request();
        $request->attributes->set(self::KEY_TYPE, $type);

        if ($meta !== []) {
            self::meta($meta);
        }
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function meta(array $meta): void
    {
        if (! self::available()) {
            return;
        }

        $request = request();

        $request->attributes->set(self::KEY_META, array_merge(
            (array) $request->attributes->get(self::KEY_META, []),
            $meta
        ));
    }

    /**
     * Captures the acting user from an event.
     *
     * Required because on a logout request auth()->user() is already null by
     * the time terminate() runs, which would attribute every logout row to a
     * guest.
     */
    public static function actor(?Authenticatable $user): void
    {
        if (! self::available() || $user === null) {
            return;
        }

        $name = trim(implode(' ', array_filter([
            $user->first_name ?? null,
            $user->last_name ?? null,
        ])));

        request()->attributes->set(self::KEY_ACTOR, [
            'user_id' => $user->getAuthIdentifier(),
            'user_name' => $name !== '' ? $name : ($user->username ?? null),
            'user_email' => $user->email ?? null,
            'role_id' => $user->role_id ?? null,
            'role_name' => $user->role?->role_name ?? null,
        ]);
    }

    public static function description(string $description): void
    {
        if (! self::available()) {
            return;
        }

        request()->attributes->set(self::KEY_DESCRIPTION, Str::limit($description, 250));
    }

    public static function subject(string $type, int|string|null $id = null): void
    {
        if (! self::available()) {
            return;
        }

        request()->attributes->set(self::KEY_SUBJECT, ['type' => $type, 'id' => $id]);
    }

    public static function exception(Throwable $e): void
    {
        if (! self::available()) {
            return;
        }

        self::type(ActivityLogType::Exception);

        self::meta([
            'exception' => [
                'class' => $e::class,
                'message' => Str::limit($e->getMessage(), 500),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => config('activity-log.capture.exception_trace', false)
                    ? Str::limit($e->getTraceAsString(), 4000)
                    : null,
            ],
        ]);
    }

    private static function available(): bool
    {
        return ! app()->runningInConsole() && app()->bound('request');
    }
}
