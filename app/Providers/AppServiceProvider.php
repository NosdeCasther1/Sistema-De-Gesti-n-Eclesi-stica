<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('gestionar-elecciones', function ($user) {
            return $user->hasRole('administrador');
        });

        Gate::define('gestionar-organizaciones', function ($user) {
            return $user->hasRole('administrador');
        });
    }
}
