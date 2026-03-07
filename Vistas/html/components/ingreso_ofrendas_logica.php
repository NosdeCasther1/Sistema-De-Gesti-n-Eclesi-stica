<?php
/**
 * ingreso_ofrendas_logica.php
 * Lógica de negocio para el módulo de Control de Ofrendas.
 * Maneja: búsqueda AJAX de miembros, POST (insertar/actualizar), DELETE GET.
 * Expone: $mensaje, $print_receipt_data, $result (mysqli_result)
 */

require_once __DIR__ . '/../../../Config/conexion.php';
$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Asegurar UTF-8
if (!$conn->set_charset("utf8")) {
    die("Error cargando el conjunto de caracteres utf8");
}

// Búsqueda AJAX de miembros
if (isset($_GET['term']) || isset($_GET['q'])) {
    $busqueda = '%';
    $stmt = mysqli_prepare(
        $conn,
        "SELECT miembro_id, nombres, apellidos, no_dpi, tel_celular, email
         FROM miembros
         WHERE nombres LIKE ? OR apellidos LIKE ? OR no_dpi LIKE ? OR tel_celular LIKE ? OR email LIKE ?
         LIMIT 10"
    );
    mysqli_stmt_bind_param($stmt, "sssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $miembros = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $miembros[] = [
            'id' => $row['miembro_id'],
            'label' => $row['nombres'] . ' ' . $row['apellidos'] . ' - ' . $row['no_dpi'],
            'value' => $row['nombres'] . ' ' . $row['apellidos'],
            'nombres' => $row['nombres'],
            'apellidos' => $row['apellidos'],
            'no_dpi' => $row['no_dpi'],
            'tel_celular' => $row['tel_celular'],
            'email' => $row['email'],
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($miembros);
    exit;
}

// Eliminar ofrenda (GET)
if (isset($_GET['delete'])) {
    $ofrenda_id = $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM ofrendas WHERE ofrenda_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $ofrenda_id);
    $mensaje = mysqli_stmt_execute($stmt) ? "Ofrenda eliminada exitosamente." : "Error al eliminar: " . mysqli_error($conn);
    mysqli_stmt_close($stmt);
}

// Guardar / actualizar ofrenda (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ofrenda_id = $_POST['ofrenda_id'] ?? null;
    $miembro_id = $_POST['miembro_id'];
    $referencia = $_POST['referencia'];
    $categoria = $_POST['categoria'];
    $modo_pago = $_POST['modo_pago'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $observacion = $_POST['observacion'];

    // Validar que el miembro exista
    $check = mysqli_prepare($conn, "SELECT miembro_id FROM miembros WHERE miembro_id = ?");
    mysqli_stmt_bind_param($check, "i", $miembro_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) == 0) {
        $mensaje = "Error: El miembro seleccionado no existe.";
    } else {
        if ($ofrenda_id) {
            $stmt = mysqli_prepare($conn, "UPDATE ofrendas SET miembro_id=?, referencia=?, categoria=?, modo_pago=?, monto=?, fecha=?, observacion=? WHERE ofrenda_id=?");
            mysqli_stmt_bind_param($stmt, "isssdssi", $miembro_id, $referencia, $categoria, $modo_pago, $monto, $fecha, $observacion, $ofrenda_id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO ofrendas (miembro_id, referencia, categoria, modo_pago, monto, fecha, observacion) VALUES (?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "isssdss", $miembro_id, $referencia, $categoria, $modo_pago, $monto, $fecha, $observacion);
        }

        if (mysqli_stmt_execute($stmt)) {
            $inserted_id = $ofrenda_id ?: mysqli_insert_id($conn);
            $mensaje = $ofrenda_id ? "Ofrenda actualizada exitosamente." : "Ofrenda agregada exitosamente.";

            // Datos para imprimir recibo
            $stmt_m = mysqli_prepare($conn, "SELECT nombres, apellidos FROM miembros WHERE miembro_id = ?");
            mysqli_stmt_bind_param($stmt_m, "i", $miembro_id);
            mysqli_stmt_execute($stmt_m);
            $row_m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_m));

            $print_receipt_data = json_encode([
                'ofrenda_id' => $inserted_id,
                'nombres' => $row_m['nombres'],
                'apellidos' => $row_m['apellidos'],
                'monto' => $monto,
                'fecha' => date('d/m/Y', strtotime($fecha)),
                'modo_pago' => $modo_pago,
                'referencia' => $referencia,
            ]);
        } else {
            $mensaje = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Consulta principal para la vista
$result = mysqli_query(
    $conn,
    "SELECT o.*, m.nombres, m.apellidos, m.no_dpi, m.tel_celular
     FROM ofrendas o
     LEFT JOIN miembros m ON o.miembro_id = m.miembro_id
     ORDER BY o.ofrenda_id DESC"
);
