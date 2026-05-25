<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidato;
use App\Models\Eleccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class EleccionController extends Controller
{
    /**
     * Cambia el estado de la elección garantizando la inmutabilidad durante la transición.
     */
    public function cambiarEstado(Request $request, Eleccion $eleccion)
    {
        // Validación de permisos RBAC
        Gate::authorize('gestionar-elecciones');

        $validated = $request->validate([
            'estado' => 'required|in:activa,finalizada'
        ]);

        DB::transaction(function () use ($eleccion, $validated) {
            // Bloqueo pesimista: Evita que entren votos mientras se cambia el estado
            $eleccionBloqueada = Eleccion::where('id', $eleccion->id)->lockForUpdate()->first();

            $updateData = ['estado' => $validated['estado']];
            
            // Si se finaliza, sellamos la hora exacta para el reporte de duración
            if ($validated['estado'] === 'finalizada') {
                $updateData['fecha_fin'] = Carbon::now();
            }

            $eleccionBloqueada->update($updateData);
        });

        return response()->json([
            'status' => 'success',
            'message' => "La elección ha sido marcada como {$validated['estado']} exitosamente."
        ]);
    }

    /**
     * Sincroniza los candidatos de la elección.
     */
    public function syncCandidatos(Request $request, Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        $validated = $request->validate([
            'candidatos' => 'present|array',
            'candidatos.*.miembro_id' => 'required|exists:miembros,id',
            'candidatos.*.puesto_postulado' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($eleccion, $validated) {
            $nuevosIds = collect($validated['candidatos'] ?? [])->pluck('miembro_id')->toArray();

            // Eliminar candidatos que ya no están seleccionados
            $eleccion->candidatos()->whereNotIn('miembro_id', $nuevosIds)->delete();

            // Crear o actualizar candidatos seleccionados
            foreach ($validated['candidatos'] ?? [] as $candData) {
                $eleccion->candidatos()->updateOrCreate(
                    ['miembro_id' => $candData['miembro_id']],
                    ['puesto_postulado' => $candData['puesto_postulado']]
                );
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Candidatos actualizados correctamente.'
        ]);
    }

    /**
     * Muestra la vista dedicada del Kiosco de Votación (Pantalla Completa)
     */
    public function kiosco(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        if ($eleccion->estado !== 'activa') {
            return redirect()->route('organizaciones.index')->with('error', 'La elección no se encuentra activa.');
        }

        $eleccion->load(['candidatos.miembro', 'organizacion']);
        $organizacion = $eleccion->organizacion;
        $padronMiembros = $organizacion->miembros()->wherePivot('estado', true)->get();

        return view('elecciones.kiosco', compact('eleccion', 'organizacion', 'padronMiembros'));
    }

    /**
     * Abre una ronda específica generando un PIN aleatorio de 5 caracteres.
     */
    public function abrirRonda(Request $request, Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');
        
        $request->validate(['puesto' => 'required|string']);

        // Genera un PIN alfanumérico seguro (Ej: A9X2B)
        $pin = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));

        $eleccion->update([
            'puesto_en_curso' => $request->puesto,
            'pin_ronda' => $pin
        ]);

        return response()->json([
            'status' => 'success', 
            'pin' => $pin, 
            'puesto' => $request->puesto,
            'message' => "Ronda para {$request->puesto} abierta con PIN: {$pin}"
        ]);
    }

    /**
     * Cierra la ronda actual y destruye el PIN, expulsando a quienes no votaron a tiempo.
     */
    public function cerrarRonda(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');
        
        $eleccion->update([
            'puesto_en_curso' => null, 
            'pin_ronda' => null
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Ronda cerrada exitosamente. Los votos han sido asegurados.'
        ]);
    }

    /**
     * Regenera el PIN de la ronda activa sin cerrarla.
     */
    public function regenerarPin(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        if (!$eleccion->puesto_en_curso) {
            return response()->json(['message' => 'No hay una ronda activa para regenerar el PIN.'], 422);
        }

        $pin = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));

        $eleccion->update(['pin_ronda' => $pin]);

        return response()->json([
            'status'  => 'success',
            'pin'     => $pin,
            'message' => "Nuevo PIN generado: {$pin}",
        ]);
    }

    /**
     * Registra la asistencia manual y suma los votos anonimos a los candidatos.
     */
    public function registrarVotosManuales(Request $request, Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        $request->validate([
            'miembros' => 'array',
            'miembros.*' => 'integer|exists:miembros,id',
            'votos_candidatos' => 'array',
            'votos_candidatos.*' => 'integer|min:0',
        ]);

        DB::transaction(function () use ($request, $eleccion) {
            $puesto = $eleccion->puesto_en_curso;
            $miembrosValidos = 0;

            // 1. Registrar asistencia manual (ignora a quienes ya votaron para evitar fraude)
            if ($request->has('miembros')) {
                foreach ($request->miembros as $miembroId) {
                    $existe = DB::table('registro_votantes')
                        ->where('eleccion_id', $eleccion->id)
                        ->where('miembro_id', $miembroId)
                        ->where('puesto_votado', $puesto)
                        ->exists();

                    if (!$existe) {
                        DB::table('registro_votantes')->insert([
                            'eleccion_id' => $eleccion->id,
                            'miembro_id' => $miembroId,
                            'puesto_votado' => $puesto,
                            'modalidad' => 'manual',
                            'registrado_por_admin_id' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $miembrosValidos++;
                    }
                }
            }

            // 2. Validar auditoria (votos vs papeletas)
            $totalVotosAsignados = array_sum($request->votos_candidatos ?? []);
            if ($totalVotosAsignados > $miembrosValidos) {
                abort(422, "Auditoria fallida: Asignaste {$totalVotosAsignados} votos, pero solo marcaste {$miembrosValidos} balotas validas entregadas.");
            }

            // 3. Escrutinio ciego (sumar votos a candidatos)
            if ($request->has('votos_candidatos')) {
                foreach ($request->votos_candidatos as $candidatoId => $cantidad) {
                    if ($cantidad > 0) {
                        Candidato::where('id', $candidatoId)
                            ->where('eleccion_id', $eleccion->id)
                            ->where('puesto_postulado', $puesto)
                            ->increment('votos_manuales', (int) $cantidad);
                    }
                }
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Escrutinio manual registrado y sellado con exito.']);
    }

    /**
     * Genera el acta final de escrutinio electoral.
     */
    public function generarReporteFinal(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        $eleccion->load('organizacion');
        $organizacion = $eleccion->organizacion;
        $candidatos = Candidato::with('miembro')
            ->where('eleccion_id', $eleccion->id)
            ->get();

        $esAbsoluta = $eleccion->tipo_mayoria === 'absoluta';

        $resultados = $candidatos->groupBy('puesto_postulado')->map(function ($grupoCandidatos) use ($esAbsoluta) {
            $totalVotosPuesto = $grupoCandidatos->sum(function ($candidato) {
                return $candidato->votos_digitales + $candidato->votos_manuales;
            });

            $grupoCandidatos = $grupoCandidatos->map(function ($candidato) {
                $candidato->votos_totales = $candidato->votos_digitales + $candidato->votos_manuales;

                return $candidato;
            });

            $maxVotos = $grupoCandidatos->max('votos_totales');

            return $grupoCandidatos->map(function ($candidato) use ($totalVotosPuesto, $maxVotos, $esAbsoluta) {
                $candidato->porcentaje = $totalVotosPuesto > 0 ? round(($candidato->votos_totales / $totalVotosPuesto) * 100, 2) : 0;
                $candidato->es_ganador = false;
                $candidato->requiere_segunda_vuelta = false;

                if ($candidato->votos_totales === $maxVotos && $maxVotos > 0) {
                    if ($esAbsoluta) {
                        if ($candidato->votos_totales > ($totalVotosPuesto / 2)) {
                            $candidato->es_ganador = true;
                        } else {
                            $candidato->requiere_segunda_vuelta = true;
                        }
                    } else {
                        $candidato->es_ganador = true;
                    }
                }

                return $candidato;
            })->sortByDesc('votos_totales')->values();
        });

        $totalPadron = $organizacion->miembros()->wherePivot('estado', true)->count();
        $totalVotantesUnicos = DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->distinct()
            ->count('miembro_id');

        $config = \App\Models\Configuracion::first() ?? \App\Models\Configuracion::create(['nombre_iglesia' => 'AD REY DE REYES', 'moneda' => 'Q']);
        $logoBase64 = null;
        if ($config && $config->logo) {
            $path = storage_path('app/public/config/' . $config->logo);
            if (file_exists($path)) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('elecciones.reporte_escrutinio', compact(
            'eleccion',
            'organizacion',
            'resultados',
            'totalPadron',
            'totalVotantesUnicos',
            'config',
            'logoBase64'
        ));

        return $pdf->stream("Acta_Escrutinio_{$eleccion->id}.pdf");
    }

    /**
     * Muestra la pantalla de proyeccion en vivo.
     */
    public function liveScreen(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        return view('elecciones.live', compact('eleccion'));
    }

    /**
     * Retorna el conteo actual de votos en formato JSON para polling.
     */
    public function liveData(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        $puesto = $eleccion->puesto_en_curso;

        if (!$puesto) {
            return response()->json(['status' => 'waiting', 'candidatos' => [], 'total_votos' => 0]);
        }

        $candidatos = Candidato::with('miembro')
            ->where('eleccion_id', $eleccion->id)
            ->where('puesto_postulado', $puesto)
            ->get()
            ->map(function ($candidato) {
                return [
                    'id' => $candidato->id,
                    'nombre' => $candidato->miembro->nombres . ' ' . $candidato->miembro->apellidos,
                    'votos' => $candidato->votos_digitales + $candidato->votos_manuales,
                ];
            })
            ->sortByDesc('votos')
            ->values();

        return response()->json([
            'status' => 'active',
            'puesto' => strtoupper($puesto),
            'total_votos' => $candidatos->sum('votos'),
            'candidatos' => $candidatos,
        ]);
    }
}
