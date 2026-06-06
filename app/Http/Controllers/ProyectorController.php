<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleccion;
use Illuminate\Support\Facades\Gate;

class ProyectorController extends Controller
{
    /**
     * Muestra la pantalla de proyección del PIN en vivo.
     */
    public function pinScreen(Eleccion $eleccion)
    {
        Gate::authorize('gestionar-elecciones');

        return view('elecciones.proyector_pin', compact('eleccion'));
    }
}
