<?php
// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $familia_id = $_POST['familia_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $genero = $_POST['genero'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];

    if ($id) {
        // Actualizar miembro existente
        $sql = "UPDATE miembros SET familia_id = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, telefono = ?, email = ?, estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssi", $familia_id, $nombre, $apellido, $fecha_nacimiento, $genero, $telefono, $email, $estado, $id);
    } else {
        // Insertar nuevo miembro
        $sql = "INSERT INTO miembros (familia_id, nombre, apellido, fecha_nacimiento, genero, telefono, email, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $familia_id, $nombre, $apellido, $fecha_nacimiento, $genero, $telefono, $email, $estado);
    }

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = $conn->error;
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>