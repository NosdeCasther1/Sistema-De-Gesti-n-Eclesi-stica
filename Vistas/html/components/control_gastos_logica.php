<?php
/**
 * control_gastos_logica.php
 * Lógica de negocio para el módulo de Control de Gastos.
 * Maneja: helper de referencia, DELETE GET, POST (insertar/actualizar), consultas principales.
 * Expone: $mensaje, $print_receipt_data, $result (gastos), $tipos_result (tipos de gasto)
 */

require_once __DIR__ . '/../../../Config/conexion.php';
$conn = getDBConnection();

// ─── Helper: referencia hexadecimal ──────────────────────────────────────────
function generateHexReference(): string
{
    $combined = time() . mt_rand(1000, 9999);
    return strtoupper(substr(md5($combined), 0, 8));
}

// ─── Eliminar gasto (GET) ─────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM gastos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $mensaje = mysqli_stmt_execute($stmt)
        ? "Gasto eliminado exitosamente."
        : "Error al eliminar gasto: " . mysqli_error($conn);
    mysqli_stmt_close($stmt);
}

// ─── Guardar / actualizar gasto (POST) ───────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gasto_id = $_POST['gasto_id'] ?? null;
    $referencia = $gasto_id ? $_POST['referencia'] : generateHexReference();
    $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha'])));
    $monto = $_POST['monto'];
    $tipo_gasto_id = $_POST['tipo_gasto'];
    $descripcion = $_POST['descripcion'];

    if ($gasto_id) {
        $stmt = mysqli_prepare($conn, "UPDATE gastos SET fecha=?, monto=?, tipo_gasto_id=?, descripcion=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sdisi", $fecha, $monto, $tipo_gasto_id, $descripcion, $gasto_id);
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO gastos (referencia, fecha, monto, tipo_gasto_id, descripcion) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssdis", $referencia, $fecha, $monto, $tipo_gasto_id, $descripcion);
    }

    if (mysqli_stmt_execute($stmt)) {
        $inserted_id = $gasto_id ?: mysqli_insert_id($conn);
        $mensaje = $gasto_id ? "Gasto actualizado exitosamente." : "Gasto agregado exitosamente.";

        // Recuperar nombre del tipo para el comprobante
        $stmt_t = mysqli_prepare($conn, "SELECT nombre FROM tipos_gasto WHERE id = ?");
        mysqli_stmt_bind_param($stmt_t, "i", $tipo_gasto_id);
        mysqli_stmt_execute($stmt_t);
        $row_t = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_t));
        $tipo_gasto_nom = $row_t['nombre'] ?? 'Sin Categoría';

        $print_receipt_data = json_encode([
            'id' => $inserted_id,
            'referencia' => $referencia,
            'monto' => $monto,
            'fecha' => date('d/m/Y', strtotime($fecha)),
            'tipo_gasto' => $tipo_gasto_nom,
            'descripcion' => $descripcion,
        ]);
    } else {
        $mensaje = ($gasto_id ? "Error al actualizar: " : "Error al agregar: ") . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// ─── Consultas para la vista ──────────────────────────────────────────────────
$result = mysqli_query(
    $conn,
    "SELECT g.*, t.nombre AS tipo_gasto_nombre
     FROM gastos g
     JOIN tipos_gasto t ON g.tipo_gasto_id = t.id
     ORDER BY g.fecha DESC"
);

$tipos_result = mysqli_query($conn, "SELECT * FROM tipos_gasto");
