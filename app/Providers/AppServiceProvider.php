<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, fn (Login $e) => activity('auth')->causedBy($e->user)->performedOn($e->user)->event('login')->log('Signed in'));
        Event::listen(Logout::class, function (Logout $e) {
            if ($e->user) {
                activity('auth')->causedBy($e->user)->performedOn($e->user)->event('logout')->log('Signed out');
            }
        });
        Event::listen(Failed::class, fn (Failed $e) => activity('auth')
            ->event('login_failed')
            ->withProperties(['email' => $e->credentials['email'] ?? null, 'ip' => request()->ip()])
            ->log('Failed sign in attempt'));

        Gate::define('view-activity-log', fn ($user): bool => in_array($user->role instanceof \UnitEnum ? $user->role->name : $user->role, ['ADMIN', 'SUPERVISOR', 'TECHNICIAN'], true));
    }
}
