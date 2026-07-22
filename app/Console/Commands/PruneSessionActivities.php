<?php

namespace App\Console\Commands;

use App\Models\SessionActivity;
use Illuminate\Console\Command;

class PruneSessionActivities extends Command
{
    private const RETENTION_DAYS = 90;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:prune-activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete session_activities rows older than the retention window, without touching user_sessions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = SessionActivity::where('occurred_at', '<', now()->subDays(self::RETENTION_DAYS))->delete();

        $this->info("Pruned {$deleted} session activity record(s).");

        return self::SUCCESS;
    }
}
