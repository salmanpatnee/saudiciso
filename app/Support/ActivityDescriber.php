<?php

namespace App\Support;

use App\Enums\ActivityLogType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Turns a classified request into the human readable sentence shown in the
 * activity list and timeline.
 */
final class ActivityDescriber
{
    /**
     * Reuses PageVisitLabeler when a named route is available. That class is
     * shared with the legacy session-activity feature and is intentionally not
     * modified here, since changing it would retroactively alter how existing
     * rows render.
     */
    public static function label(Request $request): ?string
    {
        $routeName = $request->route()?->getName();

        if ($routeName) {
            return Str::limit(PageVisitLabeler::label($routeName), 145, '');
        }

        $segment = $request->segment(1);

        return $segment ? Str::limit(Str::headline($segment), 145, '') : null;
    }

    public static function module(Request $request): ?string
    {
        $routeName = $request->route()?->getName();

        if ($routeName) {
            return Str::limit(PageVisitLabeler::module($routeName), 58, '');
        }

        $segment = $request->segment(1);

        return $segment ? Str::limit(Str::headline($segment), 58, '') : null;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function describe(ActivityLogType $type, Request $request, array $meta = []): string
    {
        $label = self::label($request) ?? $request->path();

        $description = match ($type) {
            ActivityLogType::LoginFailed => self::failedLogin($meta),
            ActivityLogType::LoginSuccess => 'Logged in',
            ActivityLogType::Logout => 'Logged out',
            ActivityLogType::Lockout => 'Locked out after too many attempts',
            ActivityLogType::Registration => 'Registered a new account',
            ActivityLogType::PasswordReset => 'Reset account password',
            ActivityLogType::PasswordChanged => 'Changed account password',
            ActivityLogType::EmailVerification => 'Verified email address',
            ActivityLogType::FileUpload => self::fileUpload($request, $label),
            ActivityLogType::Exception => 'Error on '.$label,
            ActivityLogType::NotFound => 'Hit missing page '.$request->path(),
            ActivityLogType::PermissionDenied => 'Denied access to '.$label,
            ActivityLogType::ValidationFailed => 'Validation failed on '.$label,
            default => $type->verb().' '.$label,
        };

        return Str::limit($description, 250);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private static function failedLogin(array $meta): string
    {
        $credentials = $meta['credentials'] ?? [];
        $identifier = $credentials['username'] ?? $credentials['email'] ?? null;

        return $identifier
            ? "Failed login for '{$identifier}'"
            : 'Failed login attempt';
    }

    private static function fileUpload(Request $request, string $label): string
    {
        $count = count($request->allFiles());

        return "Uploaded {$count} file(s) to {$label}";
    }
}
