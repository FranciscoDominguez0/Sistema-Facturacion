<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrador') ? true : null;
        });

        Event::listen(function (Login $event) {
            /** @var \App\Models\User $user */
            $user = $event->user;
            
            $user->update([
                'last_login_at' => now(),
            ]);
        });
    }
}
