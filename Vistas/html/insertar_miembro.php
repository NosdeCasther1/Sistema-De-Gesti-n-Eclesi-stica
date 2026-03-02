<? php
// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Verifica si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $familia = $_POST['familia'] !== '-- Selecciona --' ? $_POST['familia'] : null;
    $nivel_estudio = $_POST['nivel_estudio'] !== '-- Selecciona --' ? $_POST['nivel_estudio'] : null;
    $cargo = $_POST['cargo'] !== '-- Selecciona --' ? $_POST['cargo'] : ;
    $estado_civil = $_POST['estado_civil'] !== '-- Selecciona --' ? $_POST['estado_civil'] : null;
    $sexo = $_POST['sexo'] !== '-- Selecciona --' ? $_POST['sexo'] : null;
    // ... otros campos ...

    // Prepara la consulta SQL para insertar en la base de datos
    $query = "INSERT INTO miembros (nombre, apellidos, ...) VALUES (?, ?, ...)";

    // Prepara la declaración
    $stmt = mysqli_prepare($conn, $query);

    // Vincula los parámetros a la declaración preparada
    mysqli_stmt_bind_param($stmt, "ss...", $nombre, $apellidos, ...);

    // Ejecuta la declaración preparada
    if (mysqli_stmt_execute($stmt)) {
        // Si la inserción es exitosa, devuelve una respuesta JSON de éxito
        echo json_encode(['success' => true]);
    } else {
        // Si hay un error, devuelve una respuesta JSON con el error
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>