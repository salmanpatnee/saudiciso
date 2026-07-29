<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Support\ActivityAnnotator;
use App\Support\ActivityPayloadMasker;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogFailedLogin
{
    /**
     * $event->credentials contains the plaintext password the visitor typed, so
     * it must never reach the database unmasked. This is the highest severity
     * leak risk in the activity logging feature.
     */
    public function handle(Failed $event): void
    {
        try {
            ActivityAnnotator::type(ActivityLogType::LoginFailed, [
                'guard' => $event->guard,
                'credentials' => ActivityPayloadMasker::mask($event->credentials),
            ]);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record failed login activity.', ['exception' => $e]);
        }
    }
}
