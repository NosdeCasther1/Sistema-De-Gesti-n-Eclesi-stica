<?php
require_once __DIR__ . '/../../Config/conexion.php';
$conn = getDBConnection();

$sql = "CREATE TABLE IF NOT EXISTS celulas_familiares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    lider_nombre VARCHAR(100) NOT NULL,
    anfitrion VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NOT NULL,
    horario VARCHAR(100) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "\n";
}
