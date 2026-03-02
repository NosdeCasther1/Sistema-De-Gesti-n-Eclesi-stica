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

// Procesar el formulario de asistencia
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $evento_id = $_POST['evento_id'];
    $miembros = $_POST['miembros'] ?? [];

    // Preparar la consulta
    $query = "INSERT INTO asistencia (miembro_id, evento_id, fecha_asistencia) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    foreach ($miembros as $miembro_id) {
        mysqli_stmt_bind_param($stmt, "iis", $miembro_id, $evento_id, $fecha);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    $mensaje = "Asistencia registrada exitosamente.";
}

// Obtener la lista de miembros
$query_miembros = "SELECT miembro_id, nombres, apellidos FROM miembros ORDER BY apellidos, nombres";
$result_miembros = mysqli_query($conn, $query_miembros);

// Obtener la lista de eventos
$query_eventos = "SELECT evento_id, nombre_evento FROM eventos ORDER BY fecha_inicio DESC";
$result_eventos = mysqli_query($conn, $query_eventos);

// Obtener las fechas de asistencia registradas
$query_fechas = "SELECT DISTINCT fecha_asistencia, e.nombre_evento, e.evento_id 
                 FROM asistencia a 
                 JOIN eventos e ON a.evento_id = e.evento_id 
                 ORDER BY fecha_asistencia DESC LIMIT 10";
$result_fechas = mysqli_query($conn, $query_fechas);
?>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Sistema de Asistencia</h1>
                    <p class="text-muted">Gestione el control de llegada de los miembros a los distintos eventos de la
                        congregación.</p>
                </div>
                <a href="MostrarEventos.php" class="btn btn-outline-secondary px-4 py-2"
                    style="border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-arrow-left me-2"></i> Volver a Eventos
                </a>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
                    style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Columna Formulario Asistencia -->
                <div class="col-md-7 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <div class="card-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-user-check text-primary me-2"></i>Registrar
                                Asistencia</h5>
                        </div>
                        <div class="card-body p-4 pt-4">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Detalles del Encuentro</h6>
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="fecha"
                                            class="form-label text-muted fw-bold small text-uppercase">Fecha de
                                            Registro</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="far fa-calendar-alt text-muted"></i></span>
                                            <input type="date" class="form-control border-start-0 ps-0" id="fecha"
                                                name="fecha" required style="border-radius: 0 8px 8px 0;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="evento_id"
                                            class="form-label text-muted fw-bold small text-uppercase">Evento
                                            Seleccionado</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fas fa-star text-muted"></i></span>
                                            <select class="form-select border-start-0 ps-0" id="evento_id"
                                                name="evento_id" required style="border-radius: 0 8px 8px 0;">
                                                <option value="" selected disabled>Elija un evento de la lista...
                                                </option>
                                                <?php while ($row = mysqli_fetch_assoc($result_eventos)): ?>
                                                    <option value="<?php echo $row['evento_id']; ?>">
                                                        <?php echo htmlspecialchars($row['nombre_evento']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-primary mb-3 mt-4 border-bottom pb-2">2. Pase de Lista</h6>

                                <!-- Buscador de miembros -->
                                <div class="mb-3">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-search text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-start-0 ps-0"
                                            id="buscar_miembro"
                                            placeholder="Escriba para buscar por nombre o apellido..."
                                            style="border-radius: 0 8px 8px 0; box-shadow: none;">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="p-2 border bg-light" id="lista_miembros"
                                        style="max-height: 300px; overflow-y: auto; border-radius: 8px;">
                                        <?php
                                        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                        while ($row = mysqli_fetch_assoc($result_miembros)):
                                            $initial = strtoupper(substr($row['nombres'], 0, 1));
                                            $avatarColor = $colors[crc32($row['miembro_id']) % count($colors)];
                                            $nombreCompleto = htmlspecialchars($row['apellidos'] . ', ' . $row['nombres']);
                                            ?>
                                            <label
                                                class="d-flex align-items-center mb-2 p-2 bg-white rounded shadow-sm border btn-miembro w-100"
                                                style="cursor: pointer; transition: all 0.2s; text-align: left;"
                                                for="miembro<?php echo $row['miembro_id']; ?>">
                                                <div class="me-3">
                                                    <input class="form-check-input ms-1 mt-0 checkbox-asistencia"
                                                        type="checkbox" name="miembros[]"
                                                        value="<?php echo $row['miembro_id']; ?>"
                                                        id="miembro<?php echo $row['miembro_id']; ?>"
                                                        style="transform: scale(1.3);">
                                                </div>
                                                <div class="text-white rounded-circle me-3 d-flex justify-content-center align-items-center flex-shrink-0"
                                                    style="width: 35px; height: 35px; font-weight: bold; font-size: 0.9rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div class="flex-grow-1 text-truncate fw-bold text-dark nombre-texto">
                                                    <?php echo $nombreCompleto; ?>
                                                </div>
                                            </label>
                                        <?php endwhile; ?>

                                        <?php if (mysqli_num_rows($result_miembros) == 0): ?>
                                            <div class="text-center p-3 text-muted scale-up">
                                                <i class="fas fa-users-slash mb-2 fs-4"></i>
                                                <p class="mb-0 small">No hay miembros registrados en el sistema.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted small mt-2 d-flex justify-content-between px-1">
                                        <span id="contador_miembros"><i class="fas fa-users me-1"></i>0
                                            seleccionados</span>
                                        <a href="#" id="seleccionar_todos"
                                            class="text-primary text-decoration-none">Seleccionar todos los visibles</a>
                                    </div>
                                </div>

                                <div class="mt-4 text-end bg-light p-3 rounded-3 mt-4 border mx-n1">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold"
                                        style="border-radius: 8px;">
                                        <i class="fas fa-save me-2"></i>Guardar Asistencias
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna Asistencias Recientes -->
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <div class="card-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-history text-muted me-2"></i>Asistencias Recientes
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="list-group list-group-flush mt-2">
                                <?php
                                mysqli_data_seek($result_fechas, 0); // Reset pointer if needed
                                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];

                                while ($row = mysqli_fetch_assoc($result_fechas)):
                                    $initialEvent = strtoupper(substr($row['nombre_evento'], 0, 1));
                                    $bgEventColor = $colors[crc32($row['evento_id']) % count($colors)];
                                    ?>
                                    <a href="ver_asistencia.php?fecha=<?php echo urlencode($row['fecha_asistencia']); ?>&evento_id=<?php echo $row['evento_id']; ?>"
                                        class="list-group-item list-group-item-action d-flex align-items-center mb-3 rounded shadow-sm border"
                                        style="transition: all 0.2s;">

                                        <div class="text-white rounded d-flex justify-content-center align-items-center me-3 flex-shrink-0"
                                            style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; background-color: <?php echo $bgEventColor; ?>;">
                                            <?php echo $initialEvent; ?>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-1 text-dark fw-bold text-truncate">
                                                <?php echo htmlspecialchars($row['nombre_evento']); ?></h6>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark border me-2"><i
                                                        class="far fa-calendar-alt text-primary opacity-75 me-1"></i><?php echo date('d M Y', strtotime($row['fecha_asistencia'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="ms-2 text-muted">
                                            <i class="fas fa-chevron-right opacity-50"></i>
                                        </div>
                                    </a>
                                <?php endwhile; ?>

                                <?php if (mysqli_num_rows($result_fechas) == 0): ?>
                                    <div class="text-center text-muted p-5 bg-light rounded-3 border">
                                        <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                        <h6>Sin Historial</h6>
                                        <p class="small mb-0">Todavía no has registrado ninguna lista de asistencia para los
                                            eventos.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    /* Efecto al seleccionar un miembro */
    .btn-miembro:hover {
        background-color: #f8f9fc !important;
        border-color: #d1d3e2 !important;
    }

    input[type=checkbox]:checked+div+div {
        color: #4e73df !important;
    }

    .btn-miembro:has(input[type=checkbox]:checked) {
        border-color: #4e73df !important;
        background-color: rgba(78, 115, 223, 0.05) !important;
        box-shadow: 0 0 0 1px #4e73df !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Función de Búsqueda
        $('#buscar_miembro').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#lista_miembros label').each(function () {
                var name = $(this).find('.nombre-texto').text().toLowerCase();
                $(this).toggle(name.indexOf(value) > -1);
            });
            actualizarContadorVisible();
        });

        // Seleccionar/Deseleccionar Múltiples visibles
        $('#seleccionar_todos').click(function (e) {
            e.preventDefault();
            var $visibles = $('#lista_miembros label:visible input[type="checkbox"]');
            var todosMarcados = $visibles.length === $visibles.filter(':checked').length;

            $visibles.prop('checked', !todosMarcados);
            actualizarContadorTotal();
        });

        // Actualizar contador centralizado
        $('.checkbox-asistencia').change(function () {
            actualizarContadorTotal();
        });

        function actualizarContadorTotal() {
            var count = $('.checkbox-asistencia:checked').length;
            $('#contador_miembros').html(`<i class="fas fa-users text-primary me-1"></i><strong class="text-primary">${count}</strong> seleccionados`);
        }

        function actualizarContadorVisible() {
            var visibles = $('#lista_miembros label:visible').length;
            // Opcional: mostrar un mensaje flotante si no hay nadie visible
        }

        actualizarContadorTotal(); // Iniciar contador en 0
    });
</script>
<?php include 'footer.php'; ?>
</body>

</html>