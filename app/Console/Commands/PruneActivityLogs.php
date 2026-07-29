<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneActivityLogs extends Command
{
    protected $signature = 'activity-log:prune {--days= : Override the configured retention window} {--chunk= : Rows deleted per statement} {--optimize : Reclaim disk after pruning}';

    protected $description = 'Delete activity_logs rows older than the configured retention window, in bounded chunks.';

    /**
     * Deletion is chunked deliberately. An unbounded DELETE on a table with
     * millions of rows means one enormous transaction, a bloated redo log and
     * long lock holds. Do not "simplify" this into a single statement.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('activity-log.retention.days', 90));
        $chunk = (int) ($this->option('chunk') ?: config('activity-log.retention.chunk', 5000));
        $cutoff = now()->subDays($days);
        $total = 0;

        do {
            $deleted = ActivityLog::query()
                ->where('occurred_at', '<', $cutoff)
                ->limit($chunk)
                ->delete();

            $total += $deleted;
        } while ($deleted > 0);

        $this->info("Pruned {$total} activity log record(s) older than {$days} day(s).");

        if ($this->option('optimize')) {
            /**
             * Takes a table lock for the duration, so it stays opt-in and
             * belongs in a maintenance window.
             */
            DB::statement('OPTIMIZE TABLE activity_logs');
            $this->info('Optimized activity_logs.');
        }

        return self::SUCCESS;
    }
}
