<?php
require_once __DIR__ . '/../../Config/conexion.php';

// Obtener la conexión
$conn = getDBConnection();

// Verificar que se recibió una consulta
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    
    // Preparar la consulta SQL
    $sql = "SELECT 
                miembro_id,
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
            )
            ORDER BY nombres ASC 
            LIMIT 10";
    
    $searchTerm = "%$query%";
    
    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Obtener los resultados
    $miembros = array();
    while ($row = $result->fetch_assoc()) {
        $miembros[] = $row;
    }
    
    // Devolver los resultados como JSON
    echo json_encode($miembros);
    
    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>