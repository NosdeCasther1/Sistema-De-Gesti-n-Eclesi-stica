<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestTransaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $miembro = \App\Models\Miembro::first();
        $cat = \App\Models\CategoriaFinanciera::where('nombre', 'Diezmos')->first();

        if ($miembro && $cat) {
            \App\Models\Transaccion::create([
                'categoria_id' => $cat->id,
                'miembro_id' => $miembro->id,
                'monto' => 500.00,
                'fecha' => now(),
                'metodo_pago' => 'Efectivo',
                'descripcion' => 'Prueba de integración de sistema'
            ]);
        }
    }
}
