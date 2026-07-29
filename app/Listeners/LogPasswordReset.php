<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Support\ActivityAnnotator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Dormant today: the application exposes no password reset routes. Correct
 * scaffolding rather than active behaviour.
 */
class LogPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        try {
            ActivityAnnotator::actor($event->user);
            ActivityAnnotator::type(ActivityLogType::PasswordReset);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record password reset activity.', ['exception' => $e]);
        }
    }
}
