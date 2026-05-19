<?php
include 'header.php';
?>
<!-- Agregar Select2 CSS para buscador en select -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php

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

// Obtener la lista de miembros con datos adicionales para identificarlos
$query_miembros = "SELECT miembro_id, nombres, apellidos, no_dpi, tel_celular FROM miembros ORDER BY apellidos, nombres";
$result_miembros = mysqli_query($conn, $query_miembros);

// Obtener la lista de eventos
$query_eventos = "SELECT evento_id, nombre_evento, fecha_inicio FROM eventos ORDER BY fecha_inicio DESC";
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
                                            <span class="input-group-text bg-white border-end-0" style="z-index: 5;"><i
                                                    class="fas fa-star text-muted"></i></span>
                                            <select class="form-select border-start-0 ps-0 select2-eventos" id="evento_id"
                                                name="evento_id" required style="border-radius: 0 8px 8px 0; width: 1%;">
                                                <option value="" selected disabled>Buscar o Escribir un evento...
                                                </option>
                                                <?php while ($row = mysqli_fetch_assoc($result_eventos)): ?>
                                                    <option value="<?php echo $row['evento_id']; ?>">
                                                        <?php echo htmlspecialchars($row['nombre_evento']) . ' (' . date('d/m/Y', strtotime($row['fecha_inicio'])) . ')'; ?>
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
                                        <span class="input-group-text bg-white border-end-0" id="search-addon"><i
                                                class="fas fa-search text-muted"></i></span>
                                        <input type="text" class="form-control bg-white border-start-0 ps-0 search-premium"
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

                                            $identificador = "Sin DPI / Tel";
                                            if (!empty($row['no_dpi'])) {
                                                $identificador = "DPI: " . $row['no_dpi'];
                                            } else if (!empty($row['tel_celular'])) {
                                                $identificador = "Tel: " . $row['tel_celular'];
                                            }
                                            ?>
                                            <label
                                                class="d-flex align-items-center mb-2 p-2 bg-white rounded shadow-sm border btn-miembro w-100"
                                                style="cursor: pointer; text-align: left;"
                                                for="miembro<?php echo $row['miembro_id']; ?>">
                                                <div class="me-3 d-flex align-items-center">
                                                    <input class="form-check-input mt-0 checkbox-asistencia" type="checkbox"
                                                        name="miembros[]" value="<?php echo $row['miembro_id']; ?>"
                                                        id="miembro<?php echo $row['miembro_id']; ?>">
                                                </div>
                                                <div class="text-white rounded-circle me-3 d-flex justify-content-center align-items-center flex-shrink-0"
                                                    style="width: 38px; height: 38px; font-weight: bold; font-size: 0.95rem; background-color: <?php echo $avatarColor; ?>; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div class="flex-grow-1 text-truncate nombre-texto">
                                                    <div class="fw-bold text-dark"
                                                        style="font-size: 0.95rem; line-height: 1.2;">
                                                        <?php echo $nombreCompleto; ?>
                                                    </div>
                                                    <div class="text-muted mt-1 param-id" style="font-size: 0.75rem;">
                                                        <i
                                                            class="fas fa-id-card me-1 opacity-50"></i><?php echo htmlspecialchars($identificador); ?>
                                                    </div>
                                                </div>
                                                <div class="ms-auto pe-2 check-indicator text-primary d-none d-md-block">
                                                    <i class="fas fa-check-circle fs-5" style="color: #1e3a8a;"></i>
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
                                    <button type="submit" class="btn btn-premium px-5 py-2 fw-bold"
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
                                        class="list-group-item list-group-item-action d-flex align-items-center recent-card">

                                        <div class="text-white rounded-circle d-flex justify-content-center align-items-center me-3 flex-shrink-0"
                                            style="width: 48px; height: 48px; font-weight: bold; font-size: 1.3rem; background-color: <?php echo $bgEventColor; ?>; margin-left: 5px; box-shadow: 0 3px 6px rgba(0,0,0,0.1);">
                                            <?php echo $initialEvent; ?>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-1 text-dark fw-bold text-truncate" style="font-size: 0.95rem;">
                                                <?php echo htmlspecialchars($row['nombre_evento']); ?>
                                            </h6>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="date-badge"><i
                                                        class="far fa-calendar-alt text-primary opacity-75 me-1"></i><?php echo date('d M Y', strtotime($row['fecha_asistencia'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="ms-2 text-muted pe-1">
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
    /* Tipografía Global Premium */
    body {
        font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* Scrollbar minimalista para la lista de miembros */
    #lista_miembros::-webkit-scrollbar {
        width: 6px;
    }

    #lista_miembros::-webkit-scrollbar-track {
        background: transparent;
    }

    #lista_miembros::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    #lista_miembros::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Efecto al seleccionar un miembro */
    .btn-miembro {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.3s ease !important;
    }

    .btn-miembro:hover {
        background-color: #f8fafc !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02) !important;
    }

    /* Checkbox personalizado invisible */
    .checkbox-asistencia {
        appearance: none;
        -webkit-appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        background-color: white;
        margin-right: 2px;
    }

    .checkbox-asistencia:checked {
        border-color: #1e3a8a;
        background-color: #1e3a8a;
    }

    .checkbox-asistencia:checked::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 11px;
        color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Estilo de la tarjeta cuando está seleccionada */
    .btn-miembro:has(input[type=checkbox]:checked) {
        border-color: #1e3a8a !important;
        background-color: rgba(30, 58, 138, 0.04) !important;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1) !important;
    }

    /* Checkmark lateral derecho emergente */
    .btn-miembro:has(input[type=checkbox]:checked) .nombre-texto {
        color: #1e3a8a !important;
    }

    .check-indicator {
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-miembro:has(input[type=checkbox]:checked) .check-indicator {
        opacity: 1;
        transform: scale(1);
    }

    /* Botón Guardar Premium */
    .btn-premium {
        background: linear-gradient(135deg, #1e3a8a, #1e40af);
        color: white;
        border: none;
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
        transition: all 0.3s ease;
    }

    .btn-premium:hover {
        background: linear-gradient(135deg, #172554, #1e3a8a);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(30, 58, 138, 0.4);
    }

    /* Focus de Inputs Premium */
    .form-control:focus, .form-select:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #1e3a8a !important;
        box-shadow: 0 0 0 0.25rem rgba(30, 58, 138, 0.1) !important;
    }

    /* Estilos Premium para Select2 para encajar con el diseño Bootstrap */
    .input-group > .select2-container--default {
        flex: 1 1 auto !important;
        width: 1% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #dee2e6 !important;
        border-left: 0 !important;
        border-radius: 0 8px 8px 0 !important;
        display: flex;
        align-items: center;
        padding-left: 4px;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057;
        line-height: normal;
        padding-left: 0;
    }
    .select2-dropdown {
        border-color: #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .select2-search__field {
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #1e3a8a !important;
    }

    /* Tarjetas Asistencias Recientes */
    .recent-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px !important;
        margin-bottom: 12px !important;
        transition: all 0.3s ease;
        background: #ffffff;
        text-decoration: none !important;
        padding: 12px;
    }

    .recent-card:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        border-color: #cbd5e1;
    }

    .date-badge {
        background-color: #f1f5f9;
        color: #475569;
        border-radius: 20px;
        padding: 4px 10px;
        font-weight: 600;
        font-size: 0.75rem;
        border: 1px solid #e2e8f0;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Inicializar Select2 en el buscador de eventos
        $('.select2-eventos').select2({
            placeholder: "Escriba para buscar un evento...",
            allowClear: true,
            theme: 'default',
            language: {
                noResults: function() {
                    return "No se encontró el evento";
                }
            }
        });

        // Función de Búsqueda
        $('#buscar_miembro').on('input keyup', function () {
            var value = $(this).val().toLowerCase().trim();
            $('#lista_miembros label').each(function () {
                var name = $(this).text().toLowerCase();
                if (name.indexOf(value) > -1) {
                    $(this).removeClass('d-none d-flex').addClass('d-flex');
                } else {
                    $(this).removeClass('d-flex').addClass('d-none');
                }
            });
            actualizarContadorVisible();
        });
        
        // Estilo focus iluminado para el input group del buscador
        $('#buscar_miembro').on('focus', function() {
            $('#search-addon').css({'border-color': '#1e3a8a'});
            $(this).prev('#search-addon').find('i').removeClass('text-muted').css('color', '#1e3a8a');
        }).on('blur', function() {
            $('#search-addon').css({'border-color': '#dee2e6'});
            $(this).prev('#search-addon').find('i').addClass('text-muted').css('color', '');
        });

        // Seleccionar/Deseleccionar Múltiples visibles
        $('#seleccionar_todos').click(function (e) {
            e.preventDefault();
            // Solo seleccionar checkboxes que NO estén dentro de labels ocultos por el buscador
            var $visibles = $('#lista_miembros label:not(.d-none) input[type="checkbox"]');
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
            var visibles = $('#lista_miembros label:not(.d-none)').length;
            // Opcional: mostrar un mensaje flotante si no hay nadie visible
        }

        actualizarContadorTotal(); // Iniciar contador en 0
    });
</script>
<?php include 'footer.php'; ?>
</body>

</html>