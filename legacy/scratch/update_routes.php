<?php
$dir = 'c:/xampp/htdocs/ProyectoIglesia/Vistas/html';
$replacements = [
    'validar_admin.php' => '/ProyectoIglesia/_/validar_admin',
    'buscar_miembros.php' => '/ProyectoIglesia/_/buscar_miembros',
    'obtener_asistencias.php' => '/ProyectoIglesia/_/obtener_asistencias',
    'obtener_calendario.php' => '/ProyectoIglesia/_/obtener_calendario',
    'procesar_restore.php' => '/ProyectoIglesia/_/procesar_restore',
    'generar_backup.php' => '/ProyectoIglesia/_/generar_backup'
];

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        foreach ($replacements as $old => $new) {
            // Reemplazar solo si está entre comillas para evitar reemplazar includes de PHP
            $content = str_replace("'$old'", "'$new'", $content);
            $content = str_replace("\"$old\"", "\"$new\"", $content);
        }
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getFilename() . "\n";
        }
    }
}
