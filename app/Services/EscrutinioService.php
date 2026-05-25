<?php

namespace App\Services;

use App\Models\Eleccion;
use App\Models\Candidato;

class EscrutinioService
{
    /**
     * Retorna los resultados agrupados por puesto y ordenados por cantidad de votos.
     */
    public function calcularResultados(Eleccion $eleccion)
    {
        return Candidato::where('eleccion_id', $eleccion->id)
            ->select('*')
            ->selectRaw('(votos_digitales + votos_manuales) as votos_count')
            ->with('miembro:id,nombres,apellidos') // Usando nombres/apellidos reales del modelo Miembro
            ->orderBy('puesto_postulado')
            ->orderByDesc('votos_count')
            ->get()
            ->groupBy('puesto_postulado');
    }
}
