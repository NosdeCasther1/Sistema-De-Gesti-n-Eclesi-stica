<?php

namespace Database\Seeders;

use App\Models\FinancialAccount;
use App\Models\FinancialCategory;
use Illuminate\Database\Seeder;

class FinancialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Caja General',
                'description' => 'Fondo principal para la operatividad general de la iglesia.',
                'initial_balance' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Caja de Jóvenes',
                'description' => 'Fondo reservado para actividades y ministerios juveniles.',
                'initial_balance' => 0.00,
                'is_active' => true,
            ],
            [
                'name' => 'Fondo de Misiones',
                'description' => 'Recursos destinados exclusivamente a obra misionera y evangelismo.',
                'initial_balance' => 0.00,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            FinancialAccount::firstOrCreate(
                ['name' => $account['name']], 
                $account
            );
        }

        $categories = [
            [
                'name' => 'Diezmos',
                'type' => 'income',
                'description' => 'Ingresos por concepto de diezmos regulares.',
                'is_active' => true,
            ],
            [
                'name' => 'Ofrendas Generales',
                'type' => 'income',
                'description' => 'Ofrendas voluntarias en servicios generales.',
                'is_active' => true,
            ],
            [
                'name' => 'Pago de Energía Eléctrica',
                'type' => 'expense',
                'description' => 'Gasto de servicios públicos (electricidad del templo).',
                'is_active' => true,
            ],
            [
                'name' => 'Ayuda Social',
                'type' => 'expense',
                'description' => 'Apoyo financiero y despensas para casos de necesidad.',
                'is_active' => true,
            ],
            [
                'name' => 'Mantenimiento del Templo',
                'type' => 'expense',
                'description' => 'Reparaciones, pintura, limpieza y conservación del edificio.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            FinancialCategory::firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type']], 
                $category
            );
        }
    }
}
