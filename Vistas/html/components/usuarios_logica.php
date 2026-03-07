<?php
// Asegurar que exista conexión a la base de datos
if (!isset($conn)) {
    require_once __DIR__ . '/../../../Config/conexion.php';
    $conn = getDBConnection();
    if (!$conn) {
        die("La conexión a la base de datos no está disponible.");
    }
}

$mensaje = '';
$tipo_mensaje = '';

// Leer mensaje de redireccion (Post-Redirect-Get)
if (!empty($_GET['msg'])) {
    $mensaje = htmlspecialchars(urldecode($_GET['msg']));
    $tipo_mensaje = ($_GET['tipo'] ?? 'info') === 'error' ? 'danger' : 'success';
}

// Ruta base para redirigir siempre al tab de usuarios
$redirect_base = $_SERVER['PHP_SELF'] . '?tab=usuarios';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // ======================================
    // LOGICA 1: AÑADIR NUEVO USUARIO
    // ======================================
    if ($_POST['action'] == 'add_user') {
        $nombres = trim($_POST['nombres']);
        $usuario = trim($_POST['usuario']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $rol = $_POST['rol'] ?? 'administrador';
        $grupo_id = !empty($_POST['grupo_id']) ? (int) $_POST['grupo_id'] : null;

        $check_query = "SELECT id_usuario FROM usuarios WHERE username = ? OR email = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("ss", $usuario, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $mensaje = "Error: El nombre de usuario o correo electrónico ya están en uso.";
            $tipo_mensaje = "danger";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO usuarios (nombres, username, password, email, role, grupo_id, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'activo')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $nombres, $usuario, $hashed_password, $email, $rol, $grupo_id);

            if ($stmt->execute()) {
                $url = $redirect_base . '&msg=' . urlencode('Usuario registrado exitosamente.') . '&tipo=success';
                echo "<script>window.location.href='$url';</script>";
                exit;
            } else {
                $url = $redirect_base . '&msg=' . urlencode('Error al registrar: ' . $conn->error) . '&tipo=error';
                echo "<script>window.location.href='$url';</script>";
                exit;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }

    // ======================================
    // LOGICA 2: EDITAR USUARIO
    // ======================================
    else if ($_POST['action'] == 'edit_user') {
        $id_usuario = (int) $_POST['id_usuario'];
        $nombres = trim($_POST['edit_nombres']);
        $usuario = trim($_POST['edit_usuario']);
        $email = trim($_POST['edit_email']);
        $rol = $_POST['edit_rol'];
        $password = trim($_POST['edit_password']);
        $grupo_id = !empty($_POST['edit_grupo_id']) ? (int) $_POST['edit_grupo_id'] : null;

        // Revisar colisión de usuarios excepto él mismo
        $check_query = "SELECT id_usuario FROM usuarios WHERE (username = ? OR email = ?) AND id_usuario != ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("ssi", $usuario, $email, $id_usuario);
        $stmt_check->execute();

        if ($stmt_check->get_result()->num_rows > 0) {
            $mensaje = "Error: El nombre de usuario o email ya le pertenece a otra cuenta.";
            $tipo_mensaje = "danger";
        } else {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE usuarios SET nombres=?, username=?, email=?, role=?, grupo_id=?, password=? WHERE id_usuario=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssisi", $nombres, $usuario, $email, $rol, $grupo_id, $hashed_password, $id_usuario);
            } else {
                $query = "UPDATE usuarios SET nombres=?, username=?, email=?, role=?, grupo_id=? WHERE id_usuario=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssii", $nombres, $usuario, $email, $rol, $grupo_id, $id_usuario);
            }

            if ($stmt->execute()) {
                $url = $redirect_base . '&msg=' . urlencode("Usuario #$id_usuario actualizado correctamente.") . '&tipo=success';
                echo "<script>window.location.href='$url';</script>";
                exit;
            } else {
                $url = $redirect_base . '&msg=' . urlencode('Error al actualizar: ' . $conn->error) . '&tipo=error';
                echo "<script>window.location.href='$url';</script>";
                exit;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }

    // ======================================
    // LOGICA 3: CAMBIAR ESTADO
    // ======================================
    else if ($_POST['action'] == 'toggle_status') {
        $id_usuario = (int) $_POST['id_usuario'];
        $nuevo_estado = ($_POST['current_status'] === 'activo') ? 'inactivo' : 'activo';

        $query = "UPDATE usuarios SET status = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nuevo_estado, $id_usuario);

        if ($stmt->execute()) {
            $url = $redirect_base . '&msg=' . urlencode('Estado cambiado a ' . strtoupper($nuevo_estado) . '.') . '&tipo=success';
            echo "<script>window.location.href='$url';</script>";
            exit;
        } else {
            $url = $redirect_base . '&msg=' . urlencode('Error al cambiar estado: ' . $conn->error) . '&tipo=error';
            echo "<script>window.location.href='$url';</script>";
            exit;
        }
        $stmt->close();
    }

    // ======================================
    // LOGICA 4: ELIMINAR USUARIO (SOFT DELETE)
    // ======================================
    else if ($_POST['action'] == 'delete_user') {
        $id_usuario_del = (int) $_POST['id_usuario'];
        $current_user_id = $_SESSION['usuario_id'] ?? 0;

        if ($current_user_id == $id_usuario_del) {
            $mensaje = "Error: No puedes eliminar tu propio usuario mientras tienes sesión iniciada.";
            $tipo_mensaje = "danger";
        } else {
            // Se usa Soft Delete actualizando deleted_at a la fecha actual y status a inactivo
            $query = "UPDATE usuarios SET deleted_at = NOW(), status = 'inactivo' WHERE id_usuario = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_usuario_del);

            if ($stmt->execute()) {
                $url = $redirect_base . '&msg=' . urlencode('El usuario ha sido eliminado del listado.') . '&tipo=success';
                echo "<script>window.location.href='$url';</script>";
                exit;
            } else {
                $url = $redirect_base . '&msg=' . urlencode('Error al eliminar: ' . $conn->error) . '&tipo=error';
                echo "<script>window.location.href='$url';</script>";
                exit;
            }
            $stmt->close();
        }
    }
}

// Obtener todos los usuarios activos y mostrar listado completo (búsqueda en vivo con JS)
$sql_users = "SELECT u.id_usuario, u.nombres, u.username, u.email, u.role, u.status, u.created_at, u.ultimo_login, u.grupo_id, g.nombre AS nombre_grupo
              FROM usuarios u
              LEFT JOIN grupos_usuarios g ON u.grupo_id = g.id
              WHERE u.deleted_at IS NULL
              ORDER BY u.created_at DESC";
$result_users = $conn->query($sql_users);

// Obtener lista de grupos para los selectores del formulario
$sql_grupos = "SELECT id, nombre FROM grupos_usuarios WHERE estado = 'activo' ORDER BY nombre ASC";
$result_grupos = $conn->query($sql_grupos);
$grupos_list = [];
if ($result_grupos) {
    while ($g = $result_grupos->fetch_assoc()) {
        $grupos_list[] = $g;
    }
}
?>