<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Miembro;
use App\Models\Familia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMiembros = Miembro::count();
        $miembrosActivos = Miembro::where('estado', true)->count();
        $totalFamilias = Familia::count();

        $distribucionSexo = Miembro::select('sexo', DB::raw('count(*) as total'))
            ->groupBy('sexo')
            ->get();

        $distribucionCivil = Miembro::select('estado_civil', DB::raw('count(*) as total'))
            ->groupBy('estado_civil')
            ->get();

        $mesActual = now()->month;
        $cumpleañeros = Miembro::whereMonth('fecha_nacimiento', $mesActual)
            ->where('estado', true)
            ->orderByRaw('DAY(fecha_nacimiento) ASC')
            ->get()
            ->map(function ($miembro) {
                $fechaNac = \Carbon\Carbon::parse($miembro->fecha_nacimiento);
                $miembro->edad_a_cumplir = now()->year - $fechaNac->year;
                $miembro->dia_cumple = $fechaNac->format('d');
                return $miembro;
            });

        $nuevosConvertidos = Miembro::whereNotNull('fecha_integracion')
            ->where('fecha_integracion', '>=', now()->subDays(90))
            ->whereIn('etapa_consolidacion', ['Nuevo', 'En Discipulado'])
            ->orderBy('fecha_integracion', 'desc')
            ->take(5)
            ->get()
            ->map(function ($miembro) {
                $miembro->dias_desde_integracion = (int) \Carbon\Carbon::parse($miembro->fecha_integracion)->diffInDays(now());
                return $miembro;
            });

        return view('dashboard.index', compact(
            'totalMiembros', 'miembrosActivos', 'totalFamilias', 
            'distribucionSexo', 'distribucionCivil', 'cumpleañeros', 'nuevosConvertidos'
        ));
    }
}
