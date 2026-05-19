<?php
$modulos = [
    'miembros' => ['Miembros.php', 'Familias.php'],
    'celulas' => ['CelulasFamiliares.php'],
    'eventos' => ['MostrarEventos.php', 'CrearEvento.php'],
    'tesoreria' => ['IngresoDiezmos.php', 'IngresoOfrendas.php', 'ControlGastos.php'],
    'bienes' => ['BienesMuebles.php'],
    'reportes' => ['reporte_balance.php', 'reporte_eventos.php', 'reporte_gastos.php', 'reporte_general.php', 'reporte_ingresos.php', 'reporte_miembros.php'],
    'configuracion' => ['configuracion.php']
];

foreach ($modulos as $mod => $files) {
    foreach ($files as $file) {
        $path = __DIR__ . '/Vistas/html/' . $file;
        $content = file_get_contents($path);

        // Ensure strictly UTF-8 binary transparency by using PHP string functions
        $inject = "require_once __DIR__ . '/../../Middleware/Permisos.php';\nPermisos::verificar('$mod');\n";

        // Insert right after the first <?php
        if (preg_match('/(<\?php\s+)/', $content, $matches)) {
            $newContent = preg_replace('/(<\?php\s+)/', "$1" . $inject, $content, 1);
            file_put_contents($path, $newContent);
            echo "Updated $file\n";
        } else {
            echo "Failed $file (no <?php found)\n";
        }
    }
}
