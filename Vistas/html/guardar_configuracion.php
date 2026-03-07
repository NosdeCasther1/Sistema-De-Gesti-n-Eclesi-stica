<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos']);
    exit;
}

$nombre_iglesia = $_POST['nombre_iglesia'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$moneda = $_POST['moneda'] ?? 'Q';

// Manejo de la imagen (Logo)
$logo_query_part = "";
$params = [$nombre_iglesia, $telefono, $correo, $direccion, $moneda];
$types = "sssss";

if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $_FILES['logo']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        // Carpeta donde se guardará (asegúrate de que existe)
        $upload_dir = __DIR__ . '/../../assets/img/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0755, true);

        $new_filename = 'logo_iglesia_' . time() . '.' . $ext;
        $dest_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest_path)) {
            $logo_query_part = ", logo_url = ?";
            $params[] = $new_filename;
            $types .= "s";

            // Opcional: Borrar el logo viejo si no es el default
        }
    }
}

$sql = "UPDATE configuracion_sistema SET 
        nombre_iglesia = ?, 
        telefono = ?, 
        correo = ?, 
        direccion = ?, 
        moneda = ? 
        $logo_query_part 
        WHERE id = (SELECT id FROM (SELECT id FROM configuracion_sistema LIMIT 1) as t)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Si falla el update genérico, tal vez la tabla estaba vacía o con id distinto. Forzamos un update sin id especifico si hay un solo row.
    $sql = "UPDATE configuracion_sistema SET nombre_iglesia = ?, telefono = ?, correo = ?, direccion = ?, moneda = ? $logo_query_part";
    $stmt = $conn->prepare($sql);
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Configuración actualizada correctamente.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
