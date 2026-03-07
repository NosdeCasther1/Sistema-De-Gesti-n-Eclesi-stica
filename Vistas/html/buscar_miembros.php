<?php
// ─── Autenticación: solo usuarios con sesión activa ───────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    die(json_encode([]));
}

require_once __DIR__ . '/../../Config/conexion.php';
$conn = getDBConnection();

// Verificar que se recibió una consulta
// Verificar que se recibió una consulta
if (isset($_POST['query']) || isset($_GET['q'])) {
    $query = isset($_POST['query']) ? $_POST['query'] : $_GET['q'];

    // Preparar la consulta SQL
    $sql = "SELECT 
                miembro_id as id,
                nombres,
                apellidos,
                no_dpi,
                tel_celular,
                email
            FROM miembros 
            WHERE estado = 'Activo'
            AND (
                nombres LIKE ? 
                OR apellidos LIKE ? 
                OR CONCAT(nombres, ' ', apellidos) LIKE ?
                OR no_dpi LIKE ?
                OR tel_celular LIKE ?
            )
            ORDER BY nombres ASC 
            LIMIT 10";

    $searchTerm = "%$query%";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Obtener los resultados
    $miembros = array();
    while ($row = $result->fetch_assoc()) {
        $miembros[] = $row;
    }

    // Devolver los resultados como JSON
    header('Content-Type: application/json');
    echo json_encode($miembros);

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>