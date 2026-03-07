<?php
/**
 * miembros_logica.php
 * Lógica de negocio para el módulo de Gestión de Miembros.
 * Maneja: conexión a BD, eliminación GET, formulario POST (insertar/actualizar), consulta principal.
 * Expone: $conn, $mensaje (string|null), $result (mysqli_result)
 */

require_once __DIR__ . '/../../../Config/conexion.php';
$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// ─── Eliminar miembro (GET) ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM miembros WHERE miembro_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $mensaje = mysqli_stmt_execute($stmt)
        ? "Miembro eliminado exitosamente."
        : "Error al eliminar miembro: " . mysqli_error($conn);
    mysqli_stmt_close($stmt);
}

// ─── Guardar / actualizar miembro (POST) ─────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $miembro_id = $_POST['miembro_id'] ?? null;
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $direccion = trim($_POST['direccion']);
    $ciudad = trim($_POST['ciudad']);
    $familia = $_POST['familia'] ?? '';
    $tel_celular = trim($_POST['tel_celular']);
    $tel_fijo = trim($_POST['tel_fijo']);
    $no_dpi = substr(preg_replace('/[^0-9]/', '', $_POST['no_dpi']), 0, 13);
    $fecha_nacimiento = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_nacimiento'])));
    $nivel_estudio = $_POST['nivel_estudio'];
    $cargo = $_POST['cargo'] ?? '';
    $estado_civil = $_POST['estado_civil'] ?? '';
    $sexo = $_POST['sexo'];
    $email = trim($_POST['email']);
    $estado = $_POST['estado'];

    // Validación de campos obligatorios
    if (
        empty($nombres) || empty($apellidos) || empty($no_dpi) || empty($_POST['fecha_nacimiento']) ||
        empty($sexo) || empty($estado_civil) || empty($direccion) || empty($tel_celular) ||
        empty($familia) || empty($estado)
    ) {
        $mensaje = "Error: Faltan campos obligatorios.";
        $isValid = false;
    } else {
        $isValid = true;
    }

    if ($isValid) {
        if ($miembro_id) {
            // Actualizar miembro existente
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE miembros SET nombres=?, apellidos=?, direccion=?, ciudad=?, familia=?, tel_celular=?, tel_fijo=?, no_dpi=?, fecha_nacimiento=?, nivel_estudio=?, cargo=?, estado_civil=?, sexo=?, email=?, estado=? WHERE miembro_id=?"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssssssi",
                $nombres,
                $apellidos,
                $direccion,
                $ciudad,
                $familia,
                $tel_celular,
                $tel_fijo,
                $no_dpi,
                $fecha_nacimiento,
                $nivel_estudio,
                $cargo,
                $estado_civil,
                $sexo,
                $email,
                $estado,
                $miembro_id
            );
        } else {
            // Insertar nuevo miembro
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO miembros (nombres, apellidos, direccion, ciudad, familia, tel_celular, tel_fijo, no_dpi, fecha_nacimiento, nivel_estudio, cargo, estado_civil, sexo, email, estado) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssssssss",
                $nombres,
                $apellidos,
                $direccion,
                $ciudad,
                $familia,
                $tel_celular,
                $tel_fijo,
                $no_dpi,
                $fecha_nacimiento,
                $nivel_estudio,
                $cargo,
                $estado_civil,
                $sexo,
                $email,
                $estado
            );
        }

        $mensaje = mysqli_stmt_execute($stmt)
            ? ($miembro_id ? "Miembro actualizado exitosamente." : "Miembro agregado exitosamente.")
            : ($miembro_id ? "Error al actualizar: " : "Error al agregar: ") . mysqli_error($conn);

        mysqli_stmt_close($stmt);
    }
}

// ─── Consulta principal para la vista ────────────────────────────────────────
$result = mysqli_query($conn, "SELECT * FROM miembros");
