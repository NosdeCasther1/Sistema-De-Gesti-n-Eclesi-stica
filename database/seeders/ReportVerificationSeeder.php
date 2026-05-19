<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Miembro;
use App\Models\Familia;
use App\Models\Transaccion;
use App\Models\CategoriaFinanciera;
use App\Models\Celula;
use App\Models\Asistencia;

class ReportVerificationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Verificar Origen: BAUTIZADOS
        $miembros = Miembro::take(5)->get();
        foreach($miembros as $m) {
            $m->update(['etapa_consolidacion' => 'Bautizado']);
        }
        
        // 2. Verificar Origen: APORTES POR FAMILIA
        $familia = Familia::first();
        $catIngreso = CategoriaFinanciera::where('tipo', 'Ingreso')->first();
        
        if ($familia && $catIngreso) {
            foreach($familia->miembros as $m) {
                Transaccion::create([
                    'categoria_id' => $catIngreso->id,
                    'miembro_id' => $m->id,
                    'monto' => rand(150, 550),
                    'fecha' => now(),
                    'metodo_pago' => 'Efectivo',
                    'descripcion' => 'Aporte de verificación para reporte familiar'
                ]);
            }
        }

        // 3. Verificar Origen: ASISTENCIA (CUADRO MENSUAL)
        $celula = Celula::first();
        if ($celula) {
            foreach($celula->miembros as $m) {
                // Crear 3 asistencias aleatorias este mes
                for($i=0; $i<3; $i++) {
                    Asistencia::create([
                        'miembro_id' => $m->id,
                        'celula_id' => $celula->id,
                        'fecha' => now()->subDays(rand(1, 15)),
                        'hora' => '19:00:00'
                    ]);
                }
            }
        }
    }
}
