<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../Config/conexion.php';

    $conn = getDBConnection();

    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }

    $nombre_iglesia = $_POST['nombre_iglesia'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $moneda = $_POST['moneda'] ?? 'Q';
    $pastor_nombre = $_POST['pastor_nombre'] ?? '';

    // Manejo de la imagen (Logo)
    $logo_query_part = "";
    $params = [$nombre_iglesia, $telefono, $correo, $direccion, $moneda, $pastor_nombre];
    $types = "ssssss";

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $upload_dir = __DIR__ . '/../../assets/img/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_filename = 'logo_iglesia_' . time() . '.' . $ext;
            $dest_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest_path)) {
                $logo_query_part = ", logo_url = ?";
                $params[] = $new_filename;
                $types .= "s";
            } else {
                throw new Exception('No se pudo guardar el archivo del logo en el servidor.');
            }
        } else {
            throw new Exception('Formato de imagen no permitido.');
        }
    }

    $sql = "UPDATE configuracion_sistema SET 
            nombre_iglesia = ?, 
            telefono = ?, 
            correo = ?, 
            direccion = ?, 
            moneda = ?,
            pastor_nombre = ?
            $logo_query_part 
            WHERE id = (SELECT id FROM (SELECT id FROM configuracion_sistema LIMIT 1) as t)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $sql = "UPDATE configuracion_sistema SET nombre_iglesia = ?, telefono = ?, correo = ?, direccion = ?, moneda = ?, pastor_nombre = ? $logo_query_part";
        $stmt = $conn->prepare($sql);
    }

    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Configuración actualizada correctamente.']);
    } else {
        throw new Exception('Error al ejecutar la actualización: ' . $stmt->error);
    }

    if ($stmt) $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
