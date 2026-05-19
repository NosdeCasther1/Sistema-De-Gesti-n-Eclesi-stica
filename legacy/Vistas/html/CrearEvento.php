<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('eventos');
include 'header.php';

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Procesar el formulario de creación de evento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_evento = $_POST['nombre_evento'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $lugar = $_POST['lugar'];
    $estado = $_POST['estado'];

    // Preparar la consulta
    $query = "INSERT INTO eventos (nombre_evento, descripcion, fecha_inicio, fecha_fin, lugar, estado, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "ssssss", $nombre_evento, $descripcion, $fecha_inicio, $fecha_fin, $lugar, $estado);

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = "Evento creado exitosamente.";
    } else {
        $mensaje = "Error al crear el evento: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Crear Nuevo Evento</h1>
                    <p class="text-muted">Programe un nuevo evento o seminario en el calendario de la congregación.</p>
                </div>
                <div>
                    <a href="MostrarEventos.php" class="btn btn-outline-secondary px-4 py-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-arrow-left me-2"></i> Volver a Eventos
                    </a>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo strpos($mensaje, 'exitosamente') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show shadow-sm"
                    role="alert">
                    <i
                        class="fas <?php echo strpos($mensaje, 'exitosamente') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-plus text-primary me-2"></i>Formulario de
                        Creación de Evento</h5>
                </div>
                <div class="card-body p-4 pt-4">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Detalles Principales</h6>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="nombre_evento"
                                    class="form-label fw-bold text-muted small text-uppercase">Nombre del Evento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-heading text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="nombre_evento"
                                        name="nombre_evento" placeholder="Ej. Retiro de Jóvenes 2024" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="descripcion"
                                    class="form-label fw-bold text-muted small text-uppercase">Descripción
                                    General</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                    placeholder="Escriba los detalles, objetivo o notas importantes sobre el evento..."
                                    required style="border-radius: 8px;"></textarea>
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
                                        id="fecha_inicio" name="fecha_inicio" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label fw-bold text-muted small text-uppercase">Fecha
                                    y Hora de Fin</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="far fa-calendar-check text-muted"></i></span>
                                    <input type="datetime-local" class="form-control border-start-0 ps-0" id="fecha_fin"
                                        name="fecha_fin" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="lugar" class="form-label fw-bold text-muted small text-uppercase">Lugar
                                    Exacto</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="lugar" name="lugar"
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
                                    <option value="Programado" selected>Programado (Activo y Pendiente)</option>
                                    <option value="En Curso">En Curso (Sucediendo Ahora)</option>
                                    <option value="Finalizado">Finalizado (Completado)</option>
                                    <option value="Cancelado">Cancelado (Suspendido)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 text-end bg-light p-3 rounded-3 mt-4 border mx-n1">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 8px;">
                                <i class="fas fa-save me-2"></i>Registrar Evento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include 'footer.php'; ?>
</body>

</html>