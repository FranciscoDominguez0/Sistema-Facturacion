<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
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
        // El CSS y el JS ya se cargan con sus tags (<link rel="stylesheet"> y
        // <script type="module">), así que los preload de Vite son redundantes
        // y llenan la consola con "preloaded but not used" cuando el navegador
        // ya tiene los assets en caché.
        Vite::usePreloadTagAttributes(fn () => false);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrador') ? true : null;
        });

        Event::listen(function (Login $event) {
            /** @var User $user */
            $user = $event->user;

            $user->update([
                'last_login_at' => now(),
            ]);
        });
    }
}
