<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Models\ActivityLog;
use App\Support\ActivityAnnotator;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Annotates the login request and attributes the visitor's preceding anonymous
 * rows to the user who just signed in.
 *
 * Writes no activity row of its own; LogActivity writes the single row for this
 * request in terminate().
 */
class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        try {
            ActivityAnnotator::actor($event->user);
            ActivityAnnotator::type(ActivityLogType::LoginSuccess, [
                'guard' => $event->guard,
                'remember' => $event->remember,
            ]);

            if (! config('activity-log.visitor.backfill_on_login')) {
                return;
            }

            $this->backfillGuestRows($event);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record successful login activity.', ['exception' => $e]);
        }
    }

    /**
     * is_authenticated is deliberately left untouched. It records the state at
     * capture time, so a back-linked row reads "this was an anonymous action we
     * later attributed to user N" - which is the audit value. Overwriting it
     * would misrepresent what actually happened.
     *
     * The window is short on purpose: on a shared machine a wide one would
     * attribute another person's browsing to whoever logs in next.
     */
    private function backfillGuestRows(Login $event): void
    {
        $request = request();

        $visitorId = $request->attributes->get(ActivityAnnotator::KEY_VISITOR)
            ?? $request->cookie(config('activity-log.visitor.cookie'));

        if (! $visitorId) {
            return;
        }

        $user = $event->user;
        $name = trim(implode(' ', array_filter([$user->first_name, $user->last_name])));

        ActivityLog::query()
            ->where('visitor_id', $visitorId)
            ->whereNull('user_id')
            ->where('occurred_at', '>=', now()->subHours((int) config('activity-log.visitor.backfill_hours', 24)))
            ->update([
                'user_id' => $user->id,
                'user_name' => $name !== '' ? $name : $user->username,
                'user_email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role?->role_name,
                'linked_at' => now(),
            ]);
    }
}
