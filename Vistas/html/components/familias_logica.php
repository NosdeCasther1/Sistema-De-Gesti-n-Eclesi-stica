<?php
/**
 * familias_logica.php
 * Lógica de negocio para el módulo de Gestión de Familias.
 * Maneja: conexión, POST (insertar/actualizar), DELETE GET, SELECT principal.
 * Expone: $conn, $result (mysqli_result de familias)
 */

require_once __DIR__ . '/../../../Config/conexion.php';
$conn = getDBConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ─── Guardar / actualizar familia (POST) ──────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO familias (nombre, descripcion, estado) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $descripcion, $estado);
    } else {
        $stmt = $conn->prepare("UPDATE familias SET nombre=?, descripcion=?, estado=? WHERE id=?");
        $stmt->bind_param("sssi", $nombre, $descripcion, $estado, $id);
    }
    $stmt->execute();
    $stmt->close();
}

// ─── Eliminar familia (GET) ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM familias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// ─── Consulta principal para la vista ────────────────────────────────────────
$result = $conn->query("SELECT * FROM familias");
