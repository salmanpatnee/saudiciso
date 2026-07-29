<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Support\ActivityAnnotator;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogSuccessfulLogout
{
    /**
     * The actor must be captured from the event: by the time LogActivity's
     * terminate() runs the guard is already empty, which would attribute every
     * logout row to a guest.
     */
    public function handle(Logout $event): void
    {
        try {
            ActivityAnnotator::actor($event->user);
            ActivityAnnotator::type(ActivityLogType::Logout, ['guard' => $event->guard]);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record logout activity.', ['exception' => $e]);
        }
    }
}
