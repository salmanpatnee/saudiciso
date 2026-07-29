<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Support\ActivityAnnotator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Dormant until the login route carries throttle middleware, which it does not
 * today. Correct scaffolding rather than active behaviour.
 */
class LogLockout
{
    public function handle(Lockout $event): void
    {
        try {
            ActivityAnnotator::type(ActivityLogType::Lockout, [
                'ip' => $event->request->ip(),
            ]);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record lockout activity.', ['exception' => $e]);
        }
    }
}
