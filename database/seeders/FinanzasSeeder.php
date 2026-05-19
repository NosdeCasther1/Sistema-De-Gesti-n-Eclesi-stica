<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinanzasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Diezmos', 'tipo' => 'Ingreso'],
            ['nombre' => 'Ofrendas General', 'tipo' => 'Ingreso'],
            ['nombre' => 'Ofrenda Pro-Templo', 'tipo' => 'Ingreso'],
            ['nombre' => 'Donaciones', 'tipo' => 'Ingreso'],
            ['nombre' => 'Pago de Luz', 'tipo' => 'Gasto'],
            ['nombre' => 'Pago de Agua', 'tipo' => 'Gasto'],
            ['nombre' => 'Mantenimiento', 'tipo' => 'Gasto'],
            ['nombre' => 'Ayuda Social', 'tipo' => 'Gasto'],
            ['nombre' => 'Salarios / Honorarios', 'tipo' => 'Gasto'],
        ];

        foreach ($categorias as $cat) {
            \App\Models\CategoriaFinanciera::create($cat);
        }
    }
}
