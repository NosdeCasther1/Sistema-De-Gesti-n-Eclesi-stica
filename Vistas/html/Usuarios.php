<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

$mensaje = '';
$tipo_mensaje = '';

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
            $query = "INSERT INTO usuarios (nombres, username, password, email, role, created_at, status) VALUES (?, ?, ?, ?, ?, NOW(), 'activo')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $nombres, $usuario, $hashed_password, $email, $rol);

            if ($stmt->execute()) {
                $mensaje = "Usuario registrado exitosamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al registrar el usuario: " . $conn->error;
                $tipo_mensaje = "danger";
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
    
    // ======================================
    // LOGICA 2: EDITAR USUARIO
    // ======================================
    else if ($_POST['action'] == 'edit_user') {
        $id_usuario = (int)$_POST['id_usuario'];
        $nombres = trim($_POST['edit_nombres']);
        $usuario = trim($_POST['edit_usuario']);
        $email = trim($_POST['edit_email']);
        $rol = $_POST['edit_rol'];
        $password = trim($_POST['edit_password']);
        
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
                $query = "UPDATE usuarios SET nombres=?, username=?, email=?, role=?, password=? WHERE id_usuario=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssi", $nombres, $usuario, $email, $rol, $hashed_password, $id_usuario);
            } else {
                $query = "UPDATE usuarios SET nombres=?, username=?, email=?, role=? WHERE id_usuario=?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssi", $nombres, $usuario, $email, $rol, $id_usuario);
            }
            
            if ($stmt->execute()) {
                $mensaje = "Usuario #$id_usuario actualizado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar usuario: " . $conn->error;
                $tipo_mensaje = "danger";
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
    
    // ======================================
    // LOGICA 3: CAMBIAR ESTADO
    // ======================================
    else if ($_POST['action'] == 'toggle_status') {
        $id_usuario = (int)$_POST['id_usuario'];
        $nuevo_estado = ($_POST['current_status'] === 'activo') ? 'inactivo' : 'activo';
        
        $query = "UPDATE usuarios SET status = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nuevo_estado, $id_usuario);
        
        if ($stmt->execute()) {
            $mensaje = "El estado del usuario ha cambiado a ".strtoupper($nuevo_estado).".";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al cambiar estado: " . $conn->error;
            $tipo_mensaje = "danger";
        }
        $stmt->close();
    }
    
    // ======================================
    // LOGICA 4: ELIMINAR USUARIO (SOFT DELETE)
    // ======================================
    else if ($_POST['action'] == 'delete_user') {
        $id_usuario_del = (int)$_POST['id_usuario'];
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
                $mensaje = "El usuario ha sido eliminado permanentemente del listado principal.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al eliminar el usuario: " . $conn->error;
                $tipo_mensaje = "danger";
            }
            $stmt->close();
        }
    }
}

// Obtener todos los usuarios activos y mostrar listado completo (búsqueda en vivo con JS)
$sql_users = "SELECT id_usuario, nombres, username, email, role, status, created_at, ultimo_login 
              FROM usuarios 
              WHERE deleted_at IS NULL
              ORDER BY created_at DESC";
$result_users = $conn->query($sql_users);
?>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show d-flex align-items-center shadow-sm"
                    role="alert" style="border-radius: 10px;">
                    <i
                        class="fas <?php echo $tipo_mensaje == 'danger' ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?> fs-4 me-3"></i>
                    <div>
                        <?php echo $mensaje; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Gestión de Usuarios</h1>
                    <p class="text-muted">Administra el acceso, roles e información de los usuarios del sistema.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 8px; font-weight: 500;"
                        data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                        <i class="fas fa-plus me-2"></i>Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- Card de Contenido Premium -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-4">

                    <!-- Filtro de Búsqueda Rápida (Live Search) -->
                    <div class="mb-4 bg-light p-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.05);">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <label class="form-label text-muted small fw-bold mb-2 text-uppercase letter-spacing-1">
                                    <i class="fas fa-search me-1"></i> Búsqueda Rápida
                                </label>
                                <div class="input-group input-group-lg shadow-sm"
                                    style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-white border-0 text-primary">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="searchInput" class="form-control border-0"
                                        placeholder="Escribe nombre, usuario, rol o email..."
                                        style="font-size: 0.95rem; box-shadow: none;">
                                </div>
                            </div>
                            <div class="col-md-7 d-flex justify-content-end mt-3 mt-md-0">
                                <!-- Botones adicionales o info estadística futura acá -->
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fs-6 my-auto">
                                    <i class="fas fa-users me-1"></i> Total: <span id="userCount"
                                        class="fw-bold"><?php echo $result_users->num_rows; ?></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de resultados -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Cód.</th>
                                    <th>Usuario</th>
                                    <th>Credenciales</th>
                                    <th class="text-center">Rol</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-end">Último Acceso</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_usuarios = 0;
                                while ($row = $result_users->fetch_assoc()):
                                    $total_usuarios++;
                                    // Generar color de avatar (basado en Miembros.php)
                                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14'];
                                    $initials = strtoupper(substr($row['nombres'], 0, 1));
                                    $avatarColor = $colors[crc32($row['id_usuario']) % count($colors)];

                                    // Búsqueda en vivo Data (para facilitar el JS)
                                    $searchData = strtolower($row['nombres'] . " " . $row['username'] . " " . $row['email'] . " " . $row['role'] . " " . $row['status']);
                                    ?>
                                    <tr class="user-row" data-search="<?php echo htmlspecialchars($searchData); ?>">
                                        <td class="text-muted fw-bold small">
                                            #<?php echo str_pad($row['id_usuario'], 3, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                                                    style="background-color: <?php echo $avatarColor; ?>; width: 42px; height: 42px; font-weight: 600; font-size: 1.1rem; border: 2px solid white;">
                                                    <?php echo $initials; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">
                                                        <?php echo htmlspecialchars($row['nombres']); ?>
                                                    </div>
                                                    <div class="small text-muted d-flex align-items-center mt-1">
                                                        <i class="far fa-calendar-plus me-1 opacity-75"></i> Reg:
                                                        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-secondary mb-1">
                                                <i class="fas fa-user-circle me-1 text-primary opacity-50"></i>
                                                <?php echo htmlspecialchars($row['username']); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-envelope me-1 text-info opacity-50"></i>
                                                <?php echo htmlspecialchars($row['email']); ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if (strtolower($row['role']) == 'administrador'): ?>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-semibold">
                                                    <i class="fas fa-shield-alt me-1"></i> Admin
                                                </span>
                                            <?php elseif (strtolower($row['role']) == 'pastor'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-semibold">
                                                    <i class="fas fa-bible me-1"></i> Pastor
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1 fw-semibold">
                                                    <i class="fas fa-user-tie me-1"></i> Secretario
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (strtolower($row['status']) == 'activo'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-semibold">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i> Activo
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1 fw-semibold">
                                                    <i class="fas fa-ban me-1"></i> Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if (!empty($row['ultimo_login'])): ?>
                                                <div class="small fw-bold text-dark mb-1">
                                                    <?php echo date('d/m/Y', strtotime($row['ultimo_login'])); ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    <?php echo date('H:i', strtotime($row['ultimo_login'])); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border px-2 py-1"><i
                                                        class="fas fa-minus"></i> Nunca</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <!-- Botón Editar -->
                                                <button class="btn btn-sm text-primary btn-edit-user" 
                                                    title="Editar Usuario" 
                                                    style="background: rgba(78, 115, 223, 0.1); border-radius: 6px; border: 1px solid rgba(78, 115, 223, 0.2);"
                                                    data-id="<?php echo $row['id_usuario']; ?>"
                                                    data-nombres="<?php echo htmlspecialchars($row['nombres']); ?>"
                                                    data-usuario="<?php echo htmlspecialchars($row['username']); ?>"
                                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                    data-rol="<?php echo htmlspecialchars($row['role']); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <!-- Formulario Automático de Cambio de Estado -->
                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="m-0 p-0 d-inline-block form-toggle-status">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $row['status']; ?>">
                                                    
                                                    <?php if(strtolower($row['status']) == 'activo'): ?>
                                                        <button type="button" class="btn btn-sm text-danger btn-confirm-status" title="Desactivar Usuario" style="background: rgba(231, 74, 59, 0.1); border-radius: 6px; border: 1px solid rgba(231, 74, 59, 0.2);">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm text-success btn-confirm-status" title="Activar Usuario" style="background: rgba(28, 200, 138, 0.1); border-radius: 6px; border: 1px solid rgba(28, 200, 138, 0.2);">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </form>

                                                <!-- Formulario Eliminar Usuario -->
                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="m-0 p-0 d-inline-block form-delete-user">
                                                    <input type="hidden" name="action" value="delete_user">
                                                    <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                                    <button type="button" class="btn btn-sm text-danger btn-delete-user" title="Eliminar Usuario" style="background: rgba(231, 74, 59, 0.1); border-radius: 6px; border: 1px solid rgba(231, 74, 59, 0.2);">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <!-- Fila mostrada cuando no hay resultados de búsqueda -->
                                <tr id="noResultsRow"
                                    style="display: <?php echo ($result_users->num_rows == 0) ? 'table-row' : 'none'; ?>;">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                                style="width: 80px; height: 80px;">
                                                <i class="fas fa-search fa-2x text-secondary opacity-50"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">No se encontraron usuarios</h5>
                                            <p class="mb-0">No hay registros que coincidan con tu búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Nuevo Usuario Rediseñado Premium -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="modal-title fw-bold text-dark fs-4" id="modalNuevoUsuarioLabel">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle me-2 shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="modal-body px-4 pb-4 px-md-5">
                    <p class="text-muted mb-4 pb-2 border-bottom">Completa la información para autorizar un nuevo acceso
                        al CRM de la iglesia.</p>

                    <input type="hidden" name="action" value="add_user">

                    <div class="mb-4">
                        <label for="nombres"
                            class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Nombres
                            Completos</label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i
                                    class="far fa-address-card"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="nombres" name="nombres"
                                placeholder="Ej. Juan Pérez" required style="font-size: 1rem;">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="usuario"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Usuario
                                (Login)</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="far fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="usuario" name="usuario"
                                    placeholder="Ej. jperez" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="rol"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Rol en
                                Sistema</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="fas fa-users-cog"></i></span>
                                <select class="form-select border-start-0 ps-0" id="rol" name="rol" required
                                    style="font-size: 1rem;">
                                    <option value="administrador">Administrador</option>
                                    <option value="pastor">Pastor</option>
                                    <option value="secretario">Secretario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="email"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Correo
                                Electrónico</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="far fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email"
                                    placeholder="usuario@correo.com" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Contraseña</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="fas fa-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password"
                                    name="password" placeholder="Mínimo 8 caracteres" required style="font-size: 1rem;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-4 d-flex justify-content-end"
                    style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-white text-muted border px-4 py-2 me-2 shadow-sm"
                        data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-save me-2"></i>Registrar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="modal-title fw-bold text-dark fs-4" id="modalEditarUsuarioLabel">
                    <div class="bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center rounded-circle me-2 shadow-sm" style="width: 45px; height: 45px;">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    Editar Perfil de Usuario
                </h5>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="modal-body px-4 pb-4 px-md-5">
                    <p class="text-muted mb-4 pb-2 border-bottom">Actualiza la información del acceso al sistema de este usuario.</p>
                    
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="id_usuario" id="edit_id_usuario" value="">

                    <div class="mb-4">
                        <label for="edit_nombres" class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Nombres Completos</label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                            <span class="input-group-text bg-white border-end-0 text-info"><i class="far fa-address-card"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="edit_nombres" name="edit_nombres" required style="font-size: 1rem;">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="edit_usuario" class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Usuario (Login)</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i class="far fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="edit_usuario" name="edit_usuario" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_rol" class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Rol en Sistema</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i class="fas fa-users-cog"></i></span>
                                <select class="form-select border-start-0 ps-0" id="edit_rol" name="edit_rol" required style="font-size: 1rem;">
                                    <option value="administrador">Administrador</option>
                                    <option value="pastor">Pastor</option>
                                    <option value="secretario">Secretario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Correo Electrónico</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i class="far fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="edit_email" name="edit_email" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Cambiar Contraseña</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="edit_password" name="edit_password" placeholder="(Opcional) Déjalo en blanco si no la cambias" style="font-size: 0.9rem;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-4 d-flex justify-content-end" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-white text-muted border px-4 py-2 me-2 shadow-sm" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white px-4 py-2 shadow-sm" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar Instancia del modal Edit
        const myModalEdit = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));

        // ==========================
        // 1. Lógica Búsqueda Rápida
        // ==========================
        const searchInput = document.getElementById("searchInput");
        const rows = document.querySelectorAll(".user-row");
        const noResultsRow = document.getElementById("noResultsRow");
        const userCountSpan = document.getElementById("userCount");

        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const term = this.value.toLowerCase().trim();
                let hasResults = false;
                let visibleCount = 0;

                rows.forEach(row => {
                    const searchData = row.getAttribute("data-search");
                    if (searchData.includes(term)) {
                        row.style.display = "table-row";
                        hasResults = true;
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                });

                if (userCountSpan) userCountSpan.textContent = visibleCount;
                if (hasResults) {
                    noResultsRow.style.display = "none";
                } else {
                    noResultsRow.style.display = "table-row";
                }
            });
        }

        // ==========================
        // 2. Lógica Rellenar Modal Editar
        // ==========================
        const editButtons = document.querySelectorAll(".btn-edit-user");
        editButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                // Obtener metadata
                document.getElementById('edit_id_usuario').value = this.getAttribute('data-id');
                document.getElementById('edit_nombres').value = this.getAttribute('data-nombres');
                document.getElementById('edit_usuario').value = this.getAttribute('data-usuario');
                document.getElementById('edit_email').value = this.getAttribute('data-email');
                
                // Setear Rol
                const rolSelect = document.getElementById('edit_rol');
                rolSelect.value = this.getAttribute('data-rol').toLowerCase();

                // Blanquear password para no asustar
                document.getElementById('edit_password').value = '';
                
                // Mostrar el modal
                myModalEdit.show();
            });
        });

        // ==========================
        // 3. Confirmar Cambio de Estado
        // ==========================
        const confirmStatusButtons = document.querySelectorAll('.btn-confirm-status');
        confirmStatusButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.form-toggle-status');
                const isActivating = this.classList.contains('text-success'); // Verde significa que lo voy a promover a activo
                
                const questionTitle = isActivating ? '¿Activar usuario?' : '¿Desactivar usuario?';
                const questionHtml = isActivating 
                    ? 'Estarás reestableciendo los privilegios de acceso de este usuario en el sistema.'
                    : 'Esta cuenta ya no podrá conectarse al sistema hasta que la actives nuevamente.';
                const confirmIcon = isActivating ? 'success' : 'warning';
                
                Swal.fire({
                    title: questionTitle,
                    text: questionHtml,
                    icon: confirmIcon,
                    showCancelButton: true,
                    confirmButtonColor: isActivating ? '#1cc88a' : '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: isActivating ? 'Sí, activar cuenta' : 'Sí, suspender acceso',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // ==========================
        // 4. Confirmar Eliminación de Usuario
        // ==========================
        const deleteButtons = document.querySelectorAll('.btn-delete-user');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.form-delete-user');
                
                Swal.fire({
                    title: '¿Eliminar Usuario de Forma Permanente?',
                    text: 'Esta acción no se puede deshacer y el usuario perderá el acceso completamente.',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

</body>

</html>
<?php
$conn->close();
include 'footer.php';
?>