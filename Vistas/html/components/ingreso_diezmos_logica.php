<?php
/**
 * ingreso_diezmos_logica.php
 * Lógica de negocio para el módulo de Control de Diezmos.
 * Maneja: búsqueda AJAX, procesamiento POST (insertar/actualizar), eliminación GET.
 * Expone: $mensaje (string|null), $result (mysqli_result), $total_diezmos (float)
 */

require_once __DIR__ . '/../../../Config/conexion.php';
$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Procesar el formulario cuando se envía (POST → JSON)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $miembro_id = $_POST['miembro_id'];

    // Nombre completo del miembro
    $stmt = mysqli_prepare($conn, "SELECT CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM miembros WHERE miembro_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $miembro_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre_completo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Referencia automática
    $año = date('Y');
    $mes = date('m');
    $dia = date('d');
    $letras = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90));
    $numeros = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $referencia = "{$año}{$mes}{$dia}-{$letras}-{$numeros}";

    $modo_pago = $_POST['modo_pago'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];

    try {
        mysqli_begin_transaction($conn);

        if ($id) {
            // Obtener referencia existente
            $stmt = mysqli_prepare($conn, "SELECT referencia FROM diezmos WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $referencia_actual);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, "UPDATE diezmos SET miembro=?, nombre_completo=?, modo_pago=?, monto=?, fecha=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssdsi", $miembro_id, $nombre_completo, $modo_pago, $monto, $fecha, $id);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_commit($conn);
                $response = ['status' => 'success', 'message' => "Diezmo actualizado exitosamente.", 'referencia' => $referencia_actual];
            } else {
                throw new Exception(mysqli_error($conn));
            }
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO diezmos (miembro, nombre_completo, referencia, modo_pago, monto, fecha) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "ssssds", $miembro_id, $nombre_completo, $referencia, $modo_pago, $monto, $fecha);

            if (mysqli_stmt_execute($stmt)) {
                $last_id = mysqli_insert_id($conn);
                mysqli_commit($conn);
                $response = ['status' => 'success', 'message' => "Diezmo agregado exitosamente.", 'referencia' => $referencia, 'inserted_id' => $last_id];
            } else {
                throw new Exception(mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response = ['status' => 'error', 'message' => "Error: " . $e->getMessage()];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Eliminar diezmo (GET)
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($id === false) {
        $mensaje = "ID inválido.";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM diezmos WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $mensaje = mysqli_stmt_execute($stmt) ? "Diezmo eliminado exitosamente." : "Error al eliminar: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}

// Consulta principal para la vista
$result = mysqli_query(
    $conn,
    "SELECT d.*, m.miembro_id, m.nombres, m.apellidos, m.tel_celular, m.email, m.no_dpi
     FROM diezmos d
     LEFT JOIN miembros m ON d.miembro = m.miembro_id
     ORDER BY d.fecha DESC"
);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
