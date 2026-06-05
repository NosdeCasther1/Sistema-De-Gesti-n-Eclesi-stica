<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
        Paginator::useTailwind();

        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Gate::define('gestionar-elecciones', function ($user) {
            return $user->hasRole('administrador');
        });

        Gate::define('gestionar-organizaciones', function ($user) {
            return $user->hasRole('administrador');
        });

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $globalNotifications = collect();

            try {
                // 1. Próximos Eventos (7 días)
                $eventos = \App\Models\Evento::whereBetween('fecha_inicio', [now(), now()->addDays(7)])->get();
                foreach ($eventos as $evento) {
                    $globalNotifications->push([
                        'id' => 'evt_' . $evento->id,
                        'tipo' => 'evento',
                        'titulo' => 'Próximo Evento',
                        'mensaje' => $evento->titulo,
                        'fecha' => $evento->fecha_inicio,
                        'fecha_humana' => $evento->fecha_inicio->diffForHumans(),
                        'icono' => 'fas fa-calendar-alt',
                        'color' => 'text-amber-500 bg-amber-50 dark:bg-amber-500/10',
                        'url' => route('eventos.show', $evento->id)
                    ]);
                }

                // 2. Nuevos Miembros (7 días)
                $miembrosNuevos = \App\Models\Miembro::where('created_at', '>=', now()->subDays(7))->get();
                foreach ($miembrosNuevos as $miembro) {
                    $globalNotifications->push([
                        'id' => 'new_' . $miembro->id,
                        'tipo' => 'nuevo_miembro',
                        'titulo' => 'Nuevo Miembro',
                        'mensaje' => $miembro->nombres . ' ' . $miembro->apellidos,
                        'fecha' => $miembro->created_at,
                        'fecha_humana' => $miembro->created_at->diffForHumans(),
                        'icono' => 'fas fa-user-plus',
                        'color' => 'text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10',
                        'url' => route('miembros.show', $miembro->id)
                    ]);
                }

                // 3. Próximos Cumpleaños (7 días)
                $cumpleaneros = \App\Models\Miembro::whereNotNull('fecha_nacimiento')
                    ->where(function($q) {
                        $q->whereMonth('fecha_nacimiento', now()->month)
                          ->orWhereMonth('fecha_nacimiento', now()->addDays(7)->month);
                    })->get();
                
                foreach ($cumpleaneros as $miembro) {
                    $bday = $miembro->fecha_nacimiento->copy()->year(now()->year);
                    if ($bday->isPast() && !$bday->isToday()) {
                        $bday->addYear();
                    }
                    
                    if ($bday->between(now()->startOfDay(), now()->addDays(7)->endOfDay())) {
                        $dias = now()->startOfDay()->diffInDays($bday, false);
                        $tiempoStr = $dias == 0 ? '¡Es hoy!' : ($dias == 1 ? 'Mañana' : "En $dias días");

                        $globalNotifications->push([
                            'id' => 'bday_' . $miembro->id,
                            'tipo' => 'cumpleaños',
                            'titulo' => 'Cumpleaños ' . $tiempoStr,
                            'mensaje' => $miembro->nombres . ' ' . $miembro->apellidos,
                            'fecha' => $bday,
                            'fecha_humana' => $tiempoStr,
                            'icono' => 'fas fa-birthday-cake',
                            'color' => 'text-rose-500 bg-rose-50 dark:bg-rose-500/10',
                            'url' => route('miembros.show', $miembro->id)
                        ]);
                    }
                }

                // Ordenar por fecha (los más próximos/recientes primero) y tomar top 10
                // Para eventos/cumples, es fecha futura. Para nuevos, es fecha pasada.
                // Ordenaremos primero por lo que necesita atención hoy.
                $globalNotifications = $globalNotifications->sortByDesc('fecha')->take(10);
            } catch (\Exception $e) {
                // Evitar romper la vista si falla algo
            }

            $view->with('globalNotifications', $globalNotifications);
        });
    }
}
