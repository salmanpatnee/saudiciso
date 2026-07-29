<?php

namespace App\Listeners;

use App\Enums\ActivityLogType;
use App\Support\ActivityAnnotator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Dormant today: users are created by an administrator through UserController,
 * which does not dispatch Registered. Correct scaffolding rather than active
 * behaviour.
 */
class LogRegisteredUser
{
    public function handle(Registered $event): void
    {
        try {
            ActivityAnnotator::actor($event->user);
            ActivityAnnotator::type(ActivityLogType::Registration);
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->error('Failed to record registration activity.', ['exception' => $e]);
        }
    }
}
