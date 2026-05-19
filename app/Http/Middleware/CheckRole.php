<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $currentRol = session('current_rol', 'administrador');

        // El administrador siempre tiene acceso total.
        if ($currentRol === 'administrador') {
            return $next($request);
        }

        // El rol requerido por la ruta es obligatorio y no puede ser
        // sustituido por permisos dinamicos de modulo.
        if (!empty($roles) && !in_array($currentRol, $roles)) {
            return $this->deny(
                $request,
                'No tienes permisos para acceder a este recurso.',
                'Acceso denegado: Tu rol actual (' . strtoupper($currentRol) . ') no tiene permisos para acceder a esta seccion.'
            );
        }

        $rolePermissions = session('role_permissions', [
            'administrador' => ['miembros', 'familias', 'celulas', 'eventos', 'asistencia', 'tesoreria', 'reportes', 'configuracion'],
            'tesorero' => ['miembros', 'familias', 'eventos', 'asistencia', 'tesoreria', 'reportes'],
            'lider' => ['miembros', 'familias', 'celulas', 'eventos', 'asistencia'],
            'ujier' => ['asistencia'],
        ]);

        $currentModule = $this->resolveModule($request);

        if ($currentModule && !in_array($currentModule, $rolePermissions[$currentRol] ?? [])) {
            return $this->deny(
                $request,
                'No tienes permisos para acceder al modulo de ' . ucfirst($currentModule) . '.',
                'Acceso denegado: El rol actual (' . strtoupper($currentRol) . ') no tiene habilitado el acceso al modulo de ' . strtoupper($currentModule) . '.'
            );
        }

        return $next($request);
    }

    private function resolveModule(Request $request): ?string
    {
        return match (true) {
            $request->is('miembros*') => 'miembros',
            $request->is('familias*') => 'familias',
            $request->is('celulas*') => 'celulas',
            $request->is('eventos*') => 'eventos',
            $request->is('asistencia*') => 'asistencia',
            $request->is('tesoreria*') => 'tesoreria',
            $request->is('reportes*') => 'reportes',
            $request->is('configuracion*') => 'configuracion',
            default => null,
        };
    }

    private function deny(Request $request, string $jsonMessage, string $webMessage): Response
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => $jsonMessage], 403);
        }

        return redirect()->route('dashboard')->with('error', $webMessage);
    }
}
