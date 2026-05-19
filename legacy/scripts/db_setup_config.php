<?php
require 'C:\xampp\htdocs\ProyectoIglesia\Config\conexion.php';
$conn = getDBConnection();

$sql = "CREATE TABLE IF NOT EXISTS `configuracion_sistema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_iglesia` varchar(255) NOT NULL DEFAULT 'Iglesia AD Rey de Reyes',
  `telefono` varchar(50) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT 'default_logo.png',
  `moneda` varchar(10) DEFAULT 'Q',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "Tabla 'configuracion_sistema' creada o ya existe.\n";

    // Check if empty
    $res = mysqli_query($conn, "SELECT COUNT(*) FROM configuracion_sistema");
    $row = mysqli_fetch_row($res);
    if ($row[0] == 0) {
        $insert = "INSERT INTO `configuracion_sistema` (`nombre_iglesia`, `telefono`, `correo`, `direccion`) VALUES ('Iglesia AD Rey de Reyes', '12345678', 'contacto@iglesia.com', 'Ciudad')";
        if (mysqli_query($conn, $insert)) {
            echo "Valores por defecto insertados.\n";
        } else {
            echo "Error insertando: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "Tabla ya contenía datos.\n";
    }
} else {
    echo "Error creando tabla: " . mysqli_error($conn) . "\n";
}
