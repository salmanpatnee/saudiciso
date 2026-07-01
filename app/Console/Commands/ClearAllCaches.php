<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-all-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application, config, route, and view caches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $commands = [
            'cache:clear' => 'Application cache cleared',
            'config:clear' => 'Configuration cache cleared',
            'route:clear' => 'Route cache cleared',
            'view:clear' => 'Compiled views cleared',
        ];

        foreach ($commands as $command => $message) {
            $this->call($command);
            $this->info($message.'!');
        }

        $this->info('All caches cleared successfully.');

        return self::SUCCESS;
    }
}
