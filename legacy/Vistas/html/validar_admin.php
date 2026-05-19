<?php
session_start();
require_once __DIR__ . '/../../Config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    $conn = getDBConnection();
    $query = "SELECT * FROM usuarios WHERE role = 'administrador' AND status = 'activo' LIMIT 1";
    $result = $conn->query($query);
    
    if ($row = $result->fetch_assoc()) {
        // Verificar la contraseña
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_validated'] = true;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró un usuario administrador activo']);
    }
    
    $conn->close();
}
?>