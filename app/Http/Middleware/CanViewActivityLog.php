<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the activity log dashboard.
 *
 * Deliberately separate from the SuperAdmin middleware, which hardcodes user ID
 * 1 and gates ten other route groups. Making that role based is a correct
 * refactor with a large blast radius and belongs on its own. This preserves
 * today's behaviour by default while making it configurable for this module.
 */
class CanViewActivityLog
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || ! self::allows($user->id, $user->role_id)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    /**
     * Shared with the sidebar partial, which must not render a link to a 403.
     */
    public static function allows(?int $userId, ?int $roleId): bool
    {
        $userIds = (array) config('activity-log.access.user_ids', []);
        $roleIds = (array) config('activity-log.access.role_ids', []);

        return in_array($userId, $userIds, true) || in_array($roleId, $roleIds, true);
    }
}
