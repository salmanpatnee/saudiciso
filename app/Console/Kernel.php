<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Intentionally empty. This class is never loaded under the Laravel 11
     * skeleton, because bootstrap/app.php's Application::configure() binds the
     * framework console kernel instead. Anything scheduled here would silently
     * never run.
     *
     * The live schedule is the ->withSchedule() closure in bootstrap/app.php.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
