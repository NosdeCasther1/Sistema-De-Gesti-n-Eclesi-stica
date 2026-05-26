<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

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
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Gate::define('gestionar-elecciones', function ($user) {
            return $user->hasRole('administrador');
        });

        Gate::define('gestionar-organizaciones', function ($user) {
            return $user->hasRole('administrador');
        });
    }
}
