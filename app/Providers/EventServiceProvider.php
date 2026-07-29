<?php

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Listeners\LogLockout;
use App\Listeners\LogPasswordReset;
use App\Listeners\LogRegisteredUser;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\LogVerifiedEmail;
use App\Listeners\RecordUserLogin;
use App\Listeners\RecordUserLogout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            LogRegisteredUser::class,
        ],
        Login::class => [
            RecordUserLogin::class,
            LogSuccessfulLogin::class,
        ],
        Logout::class => [
            RecordUserLogout::class,
            LogSuccessfulLogout::class,
        ],
        Failed::class => [
            LogFailedLogin::class,
        ],
        Lockout::class => [
            LogLockout::class,
        ],
        PasswordReset::class => [
            LogPasswordReset::class,
        ],
        Verified::class => [
            LogVerifiedEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
