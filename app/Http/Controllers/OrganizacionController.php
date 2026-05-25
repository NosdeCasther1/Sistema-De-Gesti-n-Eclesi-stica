<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizacion;
use App\Models\Eleccion;
use App\Models\Candidato;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class OrganizacionController extends Controller
{
    /**
     * Carga el Dashboard Bento con los datos reactivos de la organización seleccionada.
     */
    public function index(Request $request)
    {
        $organizaciones = Organizacion::with('financialAccount')->where('estado', true)->get();
        $organizacionSeleccionada = $request->has('org') ? Organizacion::with('financialAccount')->findOrFail($request->org) : $organizaciones->first();

        $eleccionActiva = null;
        $candidatos = collect();
        $padronMiembros = collect();
        $votosTotales = 0;
        $puestosDisponibles = collect();
        // Todos los miembros activos Y bautizados (criterio electoral)
        $todosLosMiembros = \App\Models\Miembro::with('organizaciones')
            ->where('estado', true)
            ->where('etapa_consolidacion', 'Bautizado')
            ->select('id', 'nombres', 'apellidos', 'etapa_consolidacion')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
        // Organizaciones activas para filtro del modal de padrón
        $todasLasOrganizaciones = Organizacion::with(['miembros' => function($q) {
                $q->where('miembros.estado', true)->where('etapa_consolidacion', 'Bautizado');
            }])->where('estado', true)->orderBy('nombre')->get();
        // Organizaciones con sus miembros para el filtro del modal de candidatos
        $organizacionesConMiembros = $todasLasOrganizaciones;

        if ($organizacionSeleccionada) {
            $eleccionActiva = Eleccion::with('organizacion')->where('organizacion_id', $organizacionSeleccionada->id)->where('estado', 'activa')->first();
            $padronMiembros = $organizacionSeleccionada->miembros()
                ->wherePivot('estado', true)
                ->get()
                ->unique('id')
                ->values();

            if ($eleccionActiva) {
                $candidatos = Candidato::with('miembro')->where('eleccion_id', $eleccionActiva->id)->get();
                $puestosDisponibles = $candidatos->pluck('puesto_postulado')->unique();
                
                $votosQuery = DB::table('registro_votantes')->where('eleccion_id', $eleccionActiva->id);
                if ($eleccionActiva->puesto_en_curso) {
                    $votosTotales = $votosQuery->where('puesto_votado', $eleccionActiva->puesto_en_curso)->count();
                } else {
                    $votosTotales = $votosQuery->distinct('miembro_id')->count('miembro_id');
                }
            }
        }

        return view('organizaciones.index', compact(
            'organizaciones', 'organizacionSeleccionada', 'eleccionActiva', 'candidatos', 'padronMiembros', 'todosLosMiembros', 'votosTotales', 'organizacionesConMiembros', 'puestosDisponibles', 'todasLasOrganizaciones'
        ));
    }

    /**
     * Iniciar una nueva elección para la organización.
     */
    public function iniciarEleccion(Request $request, Organizacion $organizacion)
    {
        Gate::authorize('gestionar-elecciones');

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion_horas' => 'required|integer|min:1|max:72',
            'tipo_mayoria' => 'required|in:simple,absoluta',
        ]);

        $existeActiva = Eleccion::where('organizacion_id', $organizacion->id)->where('estado', 'activa')->exists();
        if ($existeActiva) {
            return response()->json(['status' => 'error', 'message' => 'Ya existe una elección activa para esta organización.'], 422);
        }

        $eleccion = Eleccion::create([
            'organizacion_id' => $organizacion->id,
            'titulo' => $validated['titulo'],
            'fecha_inicio' => Carbon::now(),
            'fecha_fin' => Carbon::now()->addHours($validated['duracion_horas']),
            'estado' => 'activa',
            'tipo_mayoria' => $validated['tipo_mayoria'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Elección "' . $eleccion->titulo . '" iniciada correctamente.',
            'eleccion_id' => $eleccion->id,
        ]);
    }

    /**
     * Gestionar el padrón de la organización.
     */
    public function syncMiembros(Request $request, Organizacion $organizacion)
    {
        Gate::authorize('gestionar-organizaciones');

        $request->validate([
            'miembros' => 'required|array',
            'miembros.*' => 'exists:miembros,id'
        ]);

        DB::transaction(function () use ($request, $organizacion) {
            $syncData = [];
            foreach ($request->miembros as $miembroId) {
                $syncData[$miembroId] = [
                    'puesto' => 'Miembro',
                    'fecha_asignacion' => now()->format('Y-m-d'),
                    'estado' => true
                ];
            }
            $organizacion->miembros()->syncWithoutDetaching($syncData);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Padrón de la organización actualizado correctamente.'
        ]);
    }
}
