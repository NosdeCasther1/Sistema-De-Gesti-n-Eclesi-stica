<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Miembro;
use App\Models\Ministerio;
use App\Models\Organizacion;

class WhatsappController extends Controller
{
    public function index(Request $request)
    {
        $ministerio_id = $request->input('ministerio_id');
        $organizacion_id = $request->input('organizacion_id');
        $etapa = $request->input('etapa');

        $query = Miembro::where('estado', true)->whereNotNull('telefono')->where('telefono', '!=', '');

        if ($ministerio_id) {
            $query->whereHas('ministerios', function ($q) use ($ministerio_id) {
                $q->where('ministerio_id', $ministerio_id);
            });
        }

        if ($organizacion_id) {
            $query->whereHas('organizaciones', function ($q) use ($organizacion_id) {
                $q->where('organizacion_id', $organizacion_id);
            });
        }

        if ($etapa) {
            $query->where('etapa_consolidacion', $etapa);
        }

        $miembros = $query->orderBy('nombres')->get();
        $ministerios = Ministerio::orderBy('nombre')->get();
        $organizaciones = Organizacion::orderBy('nombre')->get();
        
        $etapas = ['Nuevo', 'En Discipulado', 'Asignado a Célula', 'Bautizado'];

        return view('comunicaciones.whatsapp.index', compact('miembros', 'ministerios', 'organizaciones', 'ministerio_id', 'organizacion_id', 'etapas', 'etapa'));
    }
}
