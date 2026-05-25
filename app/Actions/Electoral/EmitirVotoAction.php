<?php

namespace App\Actions\Electoral;

use App\Models\Eleccion;
use App\Models\Candidato;
use Illuminate\Support\Facades\DB;

class EmitirVotoAction
{
    public function execute(Eleccion $eleccion, int $miembroId, Candidato $candidato, string $modalidad = 'digital', ?int $adminId = null)
    {
        return DB::transaction(function () use ($eleccion, $miembroId, $candidato, $modalidad, $adminId) {
            // 1. Bloqueo de seguridad
            $eleccionBloqueada = Eleccion::where('id', $eleccion->id)->lockForUpdate()->first();
            if ($eleccionBloqueada->estado !== 'activa') {
                abort(400, 'La elección no está activa.');
            }

            // 2. Bloqueo anti-doble voto por puesto
            $yaVoto = DB::table('registro_votantes')
                ->where('eleccion_id', $eleccion->id)
                ->where('miembro_id', $miembroId)
                ->where('puesto_votado', $candidato->puesto_postulado)
                ->lockForUpdate()
                ->exists();

            if ($yaVoto) abort(422, "El miembro ya emitió su voto para el puesto de {$candidato->puesto_postulado}.");

            // 3. Registrar que el miembro ya participó (Auditoría, NO guarda por quién votó)
            DB::table('registro_votantes')->insert([
                'eleccion_id' => $eleccion->id,
                'miembro_id' => $miembroId,
                'puesto_votado' => $candidato->puesto_postulado,
                'modalidad' => $modalidad,
                'registrado_por_admin_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Sumar el voto anónimo al candidato
            if ($modalidad === 'manual') {
                $candidato->increment('votos_manuales');
            } else {
                $candidato->increment('votos_digitales');
            }

            return true;
        });
    }
}
