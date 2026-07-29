<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Custom middleware aliases from Kernel.php
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdmin::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'manager' => \App\Http\Middleware\Manager::class,
            'operator' => \App\Http\Middleware\Operator::class,
            'user' => \App\Http\Middleware\User::class,
            'must.change.password' => \App\Http\Middleware\MustChangePassword::class,
            'activity.viewer' => \App\Http\Middleware\CanViewActivityLog::class,
        ]);

        /**
         * LogActivity is listed first so that on the way out it is the
         * outermost of the three and observes the final response. It still sits
         * inside StartSession and AddQueuedCookiesToResponse, so the session is
         * live during handle() and the queued visitor cookie reaches the
         * browser.
         */
        $middleware->web(append: [
            \App\Http\Middleware\LogActivity::class,
            \App\Http\Middleware\TrackUserActivity::class,
            \App\Http\Middleware\TrackPageVisit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Annotates the current request so the exception detail lands on the
         * row LogActivity is about to write. Returning false here would
         * suppress Laravel's own reporting, so it deliberately does not.
         *
         * This does not fire for the framework's $dontReport list (validation,
         * auth, 403/404, model-not-found). Those are captured by the middleware
         * via status_code instead, so the two mechanisms are complementary.
         */
        $exceptions->reportable(function (\Throwable $e): void {
            if (! config('activity-log.enabled') || ! config('activity-log.capture.exceptions')) {
                return;
            }

            \App\Support\ActivityAnnotator::exception($e);
        });
    })
    /**
     * The scheduler lives here, not in app/Console/Kernel.php. Under the
     * Laravel 11 skeleton Application::configure() binds the framework console
     * kernel, so that file is never loaded and anything scheduled in it never
     * runs.
     *
     * This still requires an OS level trigger. Herd does not run schedule:run.
     * On Windows create a Task Scheduler entry that runs `artisan schedule:run`
     * from the project directory, repeating every minute; on Linux use
     * `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.
     */
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('sessions:close-stale')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('sessions:prune-activities')->daily()->withoutOverlapping();
        $schedule->command('activity-log:prune')->dailyAt('02:15')->withoutOverlapping();
        $schedule->command('activity-log:resolve-geo')->everyFiveMinutes()->withoutOverlapping();
    })
    ->create();
