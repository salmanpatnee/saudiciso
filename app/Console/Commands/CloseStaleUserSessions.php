<?php

namespace App\Console\Commands;

use App\Enums\SessionStatus;
use App\Models\UserSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloseStaleUserSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:close-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close user_sessions rows that have been inactive past the session lifetime, so abandoned sessions are not left marked as active.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $closed = UserSession::query()
            ->stale(UserSession::onlineCutoff())
            ->update([
                'status' => SessionStatus::Ended->value,
                'logout_at' => DB::raw('last_activity_at'),
                'duration_seconds' => DB::raw('TIMESTAMPDIFF(SECOND, login_at, last_activity_at)'),
            ]);

        $this->info("Closed {$closed} stale user session(s).");

        return self::SUCCESS;
    }
}
