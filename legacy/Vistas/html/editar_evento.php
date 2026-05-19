<?php
include 'header.php';

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

$evento_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['evento_id']) ? intval($_POST['evento_id']) : 0);

if ($evento_id === 0) {
    echo "<script>alert('ID de evento no válido'); window.location.href='MostrarEventos.php';</script>";
    exit;
}

// Procesar el formulario de edición de evento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_evento = $_POST['nombre_evento'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $lugar = $_POST['lugar'];
    $estado = $_POST['estado'];

    // Preparar la consulta de UPDATE
    $query_update = "UPDATE eventos SET nombre_evento = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, lugar = ?, estado = ?, fecha_actualizacion = NOW() WHERE evento_id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);

    mysqli_stmt_bind_param($stmt_update, "ssssssi", $nombre_evento, $descripcion, $fecha_inicio, $fecha_fin, $lugar, $estado, $evento_id);

    if (mysqli_stmt_execute($stmt_update)) {
        $mensaje = "Evento actualizado exitosamente.";
        $tipo_alerta = "success";
    } else {
        $mensaje = "Error al actualizar el evento: " . mysqli_error($conn);
        $tipo_alerta = "danger";
    }

    mysqli_stmt_close($stmt_update);
}

// Consultar datos actuales del evento para rellenar formulario
$query = "SELECT * FROM eventos WHERE evento_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $evento_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Evento no encontrado'); window.location.href='MostrarEventos.php';</script>";
    exit;
}

$evento = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid p-3 p-md-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Editar Evento</h1>
                    <p class="text-muted">Actualice los detalles del evento seleccionado.</p>
                </div>
                <div>
                    <a href="MostrarEventos.php" class="btn btn-outline-secondary px-4 py-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-arrow-left me-2"></i> Volver a Eventos
                    </a>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show shadow-sm" role="alert">
                    <i
                        class="fas <?php echo $tipo_alerta === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-edit text-primary me-2"></i>Formulario de
                        Edición de Evento</h5>
                </div>
                <div class="card-body p-4 pt-4">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <!-- Campo oculto para el ID -->
                        <input type="hidden" name="evento_id" value="<?php echo $evento_id; ?>">

                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Detalles Principales</h6>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="nombre_evento"
                                    class="form-label fw-bold text-muted small text-uppercase">Nombre del Evento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-heading text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="nombre_evento"
                                        name="nombre_evento" placeholder="Ej. Retiro de Jóvenes 2024"
                                        value="<?php echo htmlspecialchars($evento['nombre_evento']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="descripcion"
                                    class="form-label fw-bold text-muted small text-uppercase">Descripción
                                    General</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                    placeholder="Escriba los detalles, objetivo o notas importantes sobre el evento..."
                                    required
                                    style="border-radius: 8px;"><?php echo htmlspecialchars($evento['descripcion']); ?></textarea>
                            </div>
                        </div>

                        <h6 class="fw-bold text-primary mb-3 mt-4 border-bottom pb-2">2. Programación y Ubicación</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="fecha_inicio"
                                    class="form-label fw-bold text-muted small text-uppercase">Fecha y Hora de
                                    Inicio</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="datetime-local" class="form-control border-start-0 ps-0"
                                        id="fecha_inicio" name="fecha_inicio"
                                        value="<?php echo date('Y-m-d\TH:i', strtotime($evento['fecha_inicio'])); ?>"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label fw-bold text-muted small text-uppercase">Fecha
                                    y Hora de Fin</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="far fa-calendar-check text-muted"></i></span>
                                    <input type="datetime-local" class="form-control border-start-0 ps-0" id="fecha_fin"
                                        name="fecha_fin"
                                        value="<?php echo date('Y-m-d\TH:i', strtotime($evento['fecha_fin'])); ?>"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="lugar" class="form-label fw-bold text-muted small text-uppercase">Lugar
                                    Exacto</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="lugar" name="lugar"
                                        value="<?php echo htmlspecialchars($evento['lugar']); ?>"
                                        placeholder="Ej. Auditorio Principal" required>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-primary mb-3 mt-4 border-bottom pb-2">3. Configuración</h6>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado
                                    Inicial</label>
                                <select class="form-select form-select-lg" id="estado" name="estado"
                                    style="font-size: 1rem; border-radius: 8px;" required>
                                    <option value="Programado" <?php echo ($evento['estado'] == 'Programado' || $evento['estado'] == 'programado') ? 'selected' : ''; ?>>Programado (Activo y
                                        Pendiente)</option>
                                    <option value="En Curso" <?php echo ($evento['estado'] == 'En Curso' || $evento['estado'] == 'en curso') ? 'selected' : ''; ?>>En Curso (Sucediendo
                                        Ahora)</option>
                                    <option value="Finalizado" <?php echo ($evento['estado'] == 'Finalizado' || $evento['estado'] == 'finalizado') ? 'selected' : ''; ?>>Finalizado (Completado)
                                    </option>
                                    <option value="Cancelado" <?php echo ($evento['estado'] == 'Cancelado' || $evento['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado (Suspendido)
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 text-end bg-light p-3 rounded-3 mt-4 border mx-n1">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 8px;">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include 'footer.php'; ?>