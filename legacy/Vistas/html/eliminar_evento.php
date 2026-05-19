<?php
// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener el ID del evento a eliminar desde la URL (método GET)
$evento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($evento_id > 0) {
    // Preparar la consulta DELETE
    $query = "DELETE FROM eventos WHERE evento_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "i", $evento_id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirigir a MostrarEventos.php tras éxito
        echo "<script>window.location.href='MostrarEventos.php';</script>";
        exit;
    } else {
        // Mostrar alerta en caso de error
        echo "<script>alert('Error al intentar eliminar el evento de la base de datos: " . addslashes(mysqli_error($conn)) . "'); window.location.href='MostrarEventos.php';</script>";
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    // ID inválido
    echo "<script>alert('El ID del evento proporcionado no es válido.'); window.location.href='MostrarEventos.php';</script>";
    exit;
}

mysqli_close($conn);
?>