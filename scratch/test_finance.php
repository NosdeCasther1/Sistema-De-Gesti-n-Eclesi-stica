<?php

use App\Models\Miembro;
use App\Models\CategoriaFinanciera;
use App\Models\Transaccion;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$miembro = Miembro::first();
$cat = CategoriaFinanciera::where('nombre', 'Diezmos')->first();

if (!$miembro || !$cat) {
    die("Error: No se encontró miembro o categoría para la prueba.\n");
}

$transaccion = Transaccion::create([
    'categoria_id' => $cat->id,
    'miembro_id' => $miembro->id,
    'monto' => 500.00,
    'fecha' => now(),
    'metodo_pago' => 'Efectivo',
    'descripcion' => 'Prueba de integración de sistema'
]);

echo "Registro de prueba exitoso para: " . $miembro->nombres . " " . $miembro->apellidos . "\n";
echo "Monto: Q" . $transaccion->monto . "\n";
echo "Balance en Tesorería actualizado.\n";
