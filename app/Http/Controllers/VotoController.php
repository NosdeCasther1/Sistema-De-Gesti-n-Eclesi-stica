<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Electoral\EmitirVotoAction;
use App\Models\Eleccion;
use Illuminate\Support\Facades\Log;

class VotoController extends Controller
{
    /**
     * Almacena el voto de forma segura delegando la lógica transaccional al Action.
     */
    public function store(Request $request, EmitirVotoAction $emitirVotoAction)
    {
        $validated = $request->validate([
            'eleccion_id' => 'required|exists:elecciones,id',
            'candidato_id' => 'required|exists:candidatos,id',
            'miembro_id' => 'required|exists:miembros,id',
            'modalidad' => 'required|in:autoservicio,asistido,digital,manual'
        ]);

        $modalidadInput = $validated['modalidad'];
        $isAsistido = in_array($modalidadInput, ['asistido', 'manual']);

        // Seguridad: Si es voto asistido/manual, el usuario actual debe tener rol administrativo
        if ($isAsistido && !$request->user()->can('gestionar-elecciones')) {
            Log::warning('Intento de voto asistido no autorizado por el usuario ID: ' . $request->user()->id);
            abort(403, 'Permisos insuficientes para operar el modo Kiosco (Voto Asistido).');
        }

        $eleccion = Eleccion::findOrFail($validated['eleccion_id']);
        $candidato = \App\Models\Candidato::findOrFail($validated['candidato_id']); // Novedad: Instanciar modelo
        $adminId = $isAsistido ? $request->user()->id : null;
        $modalidadDB = $isAsistido ? 'manual' : 'digital';

        $emitirVotoAction->execute(
            $eleccion,
            $validated['miembro_id'],
            $candidato, // Pasamos el modelo completo para el incremento anónimo
            $modalidadDB,
            $adminId
        );

        // Si el miembro tiene el puesto de 'Votante Temporal' en la organización, lo removemos tras finalizar su voto
        $eleccion->organizacion->miembros()
            ->wherePivot('puesto', 'Votante Temporal')
            ->where('miembros.id', $validated['miembro_id'])
            ->detach();

        // Limpiar la sesión del votante para que el siguiente deba identificarse
        session()->forget('votante_miembro_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Voto oficial registrado y sellado con éxito.'
        ]);
    }
}
