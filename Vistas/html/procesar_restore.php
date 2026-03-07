<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos']);
    exit;
}

if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] != 0) {
    echo json_encode(['status' => 'error', 'message' => 'No se subió ningún archivo válido.']);
    exit;
}

$file = $_FILES['backup_file']['tmp_name'];
$filename = $_FILES['backup_file']['name'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if ($ext !== 'sql') {
    echo json_encode(['status' => 'error', 'message' => 'El archivo debe ser un script SQL válido (.sql)']);
    exit;
}

// Leer archivo
$content = file_get_contents($file);

if (empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'El archivo está vacío.']);
    exit;
}

// Para prevenir errores de llaves foráneas y variables
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

$queries = explode(";\n", $content);
$success = true;

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (!mysqli_query($conn, $query)) {
            $success = false;
            // opcional: loguear mysqli_error($conn)
        }
    }
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Base de datos restaurada correctamente.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Hubo algunos errores al restaurar, el archivo podría estar corrupto o incompleto.']);
}

$conn->close();
