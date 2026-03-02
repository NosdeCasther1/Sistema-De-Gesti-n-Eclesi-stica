<?php
require '../../Config/conexion.php';
$conn = getDBConnection();
$sql = "CREATE TABLE IF NOT EXISTS bienes_muebles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(100),
    fecha_adquisicion DATE,
    valor_estimado DECIMAL(10,2),
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Tabla bienes_muebles creada exitosamente.\n";
} else {
    echo "Error creando tabla: " . mysqli_error($conn) . "\n";
}
?>