<?php
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (isset($_GET['id'])) {
    $miembro_id = intval($_GET['id']);

    // Asistencias a eventos
    $query = "SELECT e.nombre_evento, a.fecha_asistencia, e.estado 
              FROM asistencia a 
              JOIN eventos e ON a.evento_id = e.evento_id 
              WHERE a.miembro_id = ? 
              ORDER BY a.fecha_asistencia DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $miembro_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $asistencias = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $asistencias[] = $row;
    }

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $asistencias]);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit;
}
?>