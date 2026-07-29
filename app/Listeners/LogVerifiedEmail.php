<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Support\ActivityAnnotator;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Dormant today: the application exposes no email verification routes. Correct
 * scaffolding rather than active behaviour.
 */
class LogVerifiedEmail
{
    public function handle(Verified $event): void
    {
        try {
            ActivityAnnotator::actor($event->user);
            ActivityAnnotator::type(ActivityLogType::EmailVerification);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record email verification activity.', ['exception' => $e]);
        }
    }
}
