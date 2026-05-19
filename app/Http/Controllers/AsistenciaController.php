<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use App\Models\Asistencia;
use App\Models\Celula;
use App\Models\Evento;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function scanner(Request $request)
    {
        $celula_id = $request->query('celula_id');
        $evento_id = $request->query('evento_id');
        
        $contexto = null;
        if ($celula_id) $contexto = Celula::find($celula_id);
        if ($evento_id) $contexto = Evento::find($evento_id);

        $celulas = Celula::orderBy('nombre')->get();
        $eventos = Evento::where('fecha_inicio', '>=', now()->subDays(2)->toDateString())
                         ->orderBy('fecha_inicio', 'asc')
                         ->get();

        return view('asistencia.scanner', compact('contexto', 'celula_id', 'evento_id', 'celulas', 'eventos'));
    }

    public function create(Request $request)
    {
        $celula_id = $request->query('celula_id');
        $evento_id = $request->query('evento_id');
        $miembros = Miembro::orderBy('nombres')->get();
        
        $contexto = null;
        if ($celula_id) $contexto = Celula::find($celula_id);
        if ($evento_id) $contexto = Evento::find($evento_id);

        $celulas = Celula::orderBy('nombre')->get();
        $eventos = Evento::where('fecha_inicio', '>=', now()->subDays(2)->toDateString())
                         ->orderBy('fecha_inicio', 'asc')
                         ->get();

        return view('asistencia.create', compact('miembros', 'celula_id', 'evento_id', 'contexto', 'celulas', 'eventos'));
    }

    public function registrar(Request $request)
    {
        $request->validate([
            'miembro_id' => 'required|exists:miembros,id',
            'fecha' => 'required|date',
            'hora' => 'required'
        ]);

        // Evitar duplicados el mismo día para el mismo contexto
        $exists = Asistencia::where('miembro_id', $request->miembro_id)
            ->where('fecha', $request->fecha)
            ->where('celula_id', $request->celula_id)
            ->where('evento_id', $request->evento_id)
            ->exists();

        if ($exists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Asistencia ya registrada hoy.']);
            }
            return redirect()->back()->with('error', 'Asistencia ya registrada hoy para este miembro.');
        }

        $asistencia = Asistencia::create($request->all());
        $miembro = Miembro::find($request->miembro_id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => '¡Asistencia registrada!',
                'miembro' => $miembro->nombres . ' ' . $miembro->apellidos
            ]);
        }

        return redirect()->back()->with('success', 'Asistencia registrada para ' . $miembro->nombres . '.');
    }
}
