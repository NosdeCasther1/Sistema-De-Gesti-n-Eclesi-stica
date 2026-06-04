<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ministerio = \App\Models\Ministerio::firstOrCreate(['nombre' => 'Alabanza y Adoración']);
$miembro = \App\Models\Miembro::first();

if ($miembro) {
    $miembro->ministerios()->sync([$ministerio->id]);
    
    $filteredCount = \App\Models\Miembro::whereHas('ministerios', function ($q) use ($ministerio) {
        $q->where('ministerios.id', $ministerio->id);
    })->count();
    
    echo "Filtro funciona. Miembros encontrados: " . $filteredCount . "\n";
}




