<?php
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (isset($_GET['id'])) {
    $miembro_id = intval($_GET['id']);
    $contribuciones = [];

    // Primero, obtener el nombre del miembro para buscar en la tabla "diezmos" que usa VARCHAR en "miembro" en lugar de ID (según el DB schema)
    $queryNombre = "SELECT nombres, apellidos FROM miembros WHERE miembro_id = ?";
    $stmtNombre = mysqli_prepare($conn, $queryNombre);
    mysqli_stmt_bind_param($stmtNombre, "i", $miembro_id);
    mysqli_stmt_execute($stmtNombre);
    $resultNombre = mysqli_stmt_get_result($stmtNombre);
    $nombre_completo = "";
    if ($rowNombre = mysqli_fetch_assoc($resultNombre)) {
        // En tu DB, puede que 'miembro' se haya guardado como "Nombre Apellido" o solo "Nombre"
        $nombre_completo = trim($rowNombre['nombres'] . ' ' . $rowNombre['apellidos']);
    }
    mysqli_stmt_close($stmtNombre);

    // 1. Obtener Ofrendas (esta sí usa miembro_id)
    $queryOfrendas = "SELECT 'Ofrenda' as tipo, categoria, monto, fecha, modo_pago, referencia 
                      FROM ofrendas 
                      WHERE miembro_id = ? 
                      ORDER BY fecha DESC";
    $stmtO = mysqli_prepare($conn, $queryOfrendas);
    mysqli_stmt_bind_param($stmtO, "i", $miembro_id);
    mysqli_stmt_execute($stmtO);
    $resultO = mysqli_stmt_get_result($stmtO);
    while ($row = mysqli_fetch_assoc($resultO)) {
        $contribuciones[] = $row;
    }
    mysqli_stmt_close($stmtO);

    // 2. Obtener Diezmos (buscar por nombre_completo en 'miembro')
    if (!empty($nombre_completo)) {
        // Intentamos buscar coincidencias (por ejemplo, si diezmos.miembro dice "Juan Perez")
        // O buscar por miembro_id si alguna vez se guardó así. Lo más seguro es buscar por ambos.
        $queryDiezmos = "SELECT 'Diezmo' as tipo, 'Diezmo' as categoria, monto, fecha, modo_pago, referencia 
                         FROM diezmos 
                         WHERE miembro = ? OR miembro = ? 
                         ORDER BY fecha DESC";
        $stmtD = mysqli_prepare($conn, $queryDiezmos);
        $id_str = (string) $miembro_id;
        // Buscamos si el campo miembro tiene el ID o el Nombre Completo
        mysqli_stmt_bind_param($stmtD, "ss", $nombre_completo, $id_str);
        mysqli_stmt_execute($stmtD);
        $resultD = mysqli_stmt_get_result($stmtD);
        while ($row = mysqli_fetch_assoc($resultD)) {
            $contribuciones[] = $row;
        }
        mysqli_stmt_close($stmtD);
    }

    // Ordenar todo por fecha DESC
    usort($contribuciones, function ($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $contribuciones]);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit;
}
?>