<?php
// Incluir archivo de conexión
require_once __DIR__ . '/Config/conexion.php';

try {
    // Intentar obtener la conexión a la base de datos
    $conn = getDBConnection();

    // Si la conexión es exitosa, mostrar mensaje
    echo "Conexión exitosa a la base de datos.\n";

    // Cerrar la conexión
    $conn->close();
} catch (Exception $e) {
    // Si ocurre un error, mostrar mensaje
    echo "Error de conexión: " . $e->getMessage() . "\n";
}
?>