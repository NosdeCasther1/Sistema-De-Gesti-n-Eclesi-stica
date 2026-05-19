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
    die("La conexión a la base de datos no está disponible: " . mysqli_connect_error());
}

// Consulta para obtener todos los eventos
$query = "SELECT * FROM eventos ORDER BY fecha_inicio DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
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
                    <h1 class="h2 text-dark font-weight-bold">Cartelera de Eventos</h1>
                    <p class="text-muted">Consulta, filtra y administra todas las actividades y eventos programados.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="reporte_eventos.php" class="btn btn-secondary px-4 py-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-print me-2"></i> Reporte
                    </a>
                    <a href="CrearEvento.php" class="btn btn-primary px-4 py-2"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-calendar-plus me-2"></i> Nuevo Evento
                    </a>
                </div>
            </div>

            <!-- Nav tabs -->
            <ul class="nav nav-pills mb-4" id="eventosTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 rounded-pill" id="lista-tab" data-bs-toggle="tab"
                        data-bs-target="#lista" type="button" role="tab" aria-controls="lista" aria-selected="true"
                        style="color: #555;">
                        <i class="fas fa-list me-2"></i>Vista Lista
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 ms-2 rounded-pill" id="calendario-tab" data-bs-toggle="tab"
                        data-bs-target="#calendario" type="button" role="tab" aria-controls="calendario"
                        aria-selected="false" style="color: #555;">
                        <i class="far fa-calendar-alt me-2"></i>Vista Calendario
                    </button>
                </li>
            </ul>

            <style>
                .nav-pills .nav-link.active {
                    background-color: #4e73df !important;
                    color: white !important;
                }

                /* Estilos de impresión para el calendario */
                @media print {
                    @page {
                        size: landscape;
                        /* Orientación Horizontal */
                        margin: 10mm;
                    }

                    body * {
                        visibility: hidden;
                    }

                    #calendario,
                    #calendario * {
                        visibility: visible;
                    }

                    #calendario {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                    }

                    /* Limpiar márgenes tipo tarjeta y forzar el alto del calendario */
                    #calendario .card,
                    #calendario .card-body {
                        border: none !important;
                        box-shadow: none !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }

                    .fc-view-harness {
                        height: 75vh !important;
                        /* Forzar encajar en 1 página */
                    }

                    #print-header-cal {
                        display: block !important;
                    }

                    .fc-header-toolbar,
                    .btn-print-cal {
                        display: none !important;
                    }
                }

                /* Diseño Premium para FullCalendar - Tonos Modernos Llamativos */
                #calendar {
                    font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                    color: #1e293b;
                    max-width: 1100px;
                    /* Limitar el ancho para que no se estire excesivamente en pantallas grandes */
                    margin: 0 auto;
                }

                /* Botones del Toolbar */
                .fc .fc-button-primary {
                    background: linear-gradient(135deg, #1e3a8a, #1e40af);
                    /* Navy Blue - Color Institucional */
                    color: white;
                    border: none;
                    border-radius: 8px;
                    text-transform: capitalize;
                    font-weight: 600;
                    box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
                    padding: 8px 16px;
                    transition: all 0.3s ease;
                }

                .fc .fc-button-primary:not(:disabled):active,
                .fc .fc-button-primary:not(:disabled).fc-button-active,
                .fc .fc-button-primary:hover {
                    background: linear-gradient(135deg, #172554, #1e3a8a);
                    color: white;
                    border: none;
                    transform: translateY(-2px);
                    box-shadow: 0 6px 14px rgba(30, 58, 138, 0.4);
                }

                /* Título del Mes */
                .fc .fc-toolbar-title {
                    font-size: 1.7rem;
                    font-weight: 800;
                    background: linear-gradient(135deg, #1e293b, #1e3a8a);
                    /* Tono Institucional */
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                /* Cuadrícula General */
                .fc-theme-standard td,
                .fc-theme-standard th,
                .fc-theme-standard .fc-scrollgrid {
                    border-color: #cbd5e1;
                }

                .fc-theme-standard .fc-scrollgrid {
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 8px 24px rgba(100, 116, 139, 0.08);
                    border: 1px solid #cbd5e1;
                }

                /* Cabeceras de días (Lun, Mar...) */
                .fc-col-header-cell {
                    background: linear-gradient(to bottom, #f8fafc, #f1f5f9);
                    padding: 12px 0;
                    border-bottom: 2px solid #e2e8f0;
                }

                .fc-col-header-cell-cushion {
                    color: #475569;
                    font-weight: 700;
                    text-transform: uppercase;
                    font-size: 0.85rem;
                    letter-spacing: 0.5px;
                    text-decoration: none !important;
                }

                /* Días del calendario */
                .fc-daygrid-day-number {
                    color: #475569;
                    font-weight: 600;
                    padding: 8px 10px;
                    font-size: 0.95rem;
                    text-decoration: none !important;
                }

                /* Día actual (Hoy) */
                .fc .fc-daygrid-day.fc-day-today {
                    background-color: rgba(245, 158, 11, 0.05);
                    /* Dorado muy transparente */
                }

                .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
                    background: linear-gradient(135deg, #f59e0b, #d97706);
                    /* Dorado Institucional para resaltar */
                    color: white;
                    border-radius: 50%;
                    width: 32px;
                    height: 32px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    margin: 6px;
                    font-weight: bold;
                    box-shadow: 0 3px 8px rgba(245, 158, 11, 0.4);
                }

                /* Eventos (Pills) */
                .fc-event {
                    border: none;
                    border-radius: 4px;
                    padding: 2px 4px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    cursor: pointer;
                    margin-bottom: 3px !important;
                    transition: transform 0.1s;
                }

                .fc-event:hover {
                    filter: brightness(0.9);
                    transform: scale(1.02);
                }

                .fc-daygrid-event-dot {
                    display: none;
                    /* Ocultar el puntito nativo, usaremos pildoras completas */
                }
            </style>

            <div class="tab-content" id="eventosTabsContent">
                <!-- VISTA LISTA -->
                <div class="tab-pane fade show active" id="lista" role="tabpanel" aria-labelledby="lista-tab">
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-body p-4">
                            <!-- Buscador Unificado -->
                            <div class="row mb-4 bg-light p-3 rounded align-items-end"
                                style="border: 1px solid #f0f0f0;">
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-bold mb-1"><i
                                            class="fas fa-search me-1"></i>Buscador de Eventos</label>
                                    <div class="input-group search-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" class="form-control border-start-0 border-end-0 ps-0 search-input" id="searchInput" placeholder="Buscar por Nombre del Evento o Lugar..." style="box-shadow: none;">
                                    <button class="btn bg-white border-start-0 border text-muted clear-search" type="button" id="clearSearch" style="display: none;"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table-softwys table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Detalles del Evento</th>
                                            <th style="width: 20%;">Inicio</th>
                                            <th style="width: 20%;">Fin</th>
                                            <th style="width: 15%; text-align: center;">Estado</th>
                                            <th style="width: 15%; text-align: center;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($result)):
                                            $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                            $initial = strtoupper(substr($row['nombre_evento'], 0, 1));
                                            $avatarColor = $colors[crc32($row['evento_id']) % count($colors)];
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-white rounded me-3 d-flex justify-content-center align-items-center shadow-sm flex-shrink-0"
                                                            style="width: 48px; height: 48px; font-weight: bold; font-size: 1.4rem; background-color: <?php echo $avatarColor; ?>;">
                                                            <?php echo $initial; ?>
                                                        </div>
                                                        <div class="text-truncate">
                                                            <div class="fw-bold text-dark fs-6 text-truncate"
                                                                style="max-width: 250px;"
                                                                title="<?php echo htmlspecialchars($row['nombre_evento']); ?>">
                                                                <?php echo htmlspecialchars($row['nombre_evento']); ?>
                                                            </div>
                                                            <div class="small text-muted text-truncate"
                                                                style="max-width: 250px;">
                                                                <i
                                                                    class="fas fa-map-marker-alt text-danger opacity-75 me-1"></i><?php echo htmlspecialchars($row['lugar']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="text-dark fw-bold small"><i
                                                            class="far fa-calendar-alt text-primary opacity-75 me-1"></i>
                                                        <?php echo date('d M Y', strtotime($row['fecha_inicio'])); ?></div>
                                                    <div class="text-muted small"><i
                                                            class="far fa-clock opacity-50 me-1"></i>
                                                        <?php echo date('h:i A', strtotime($row['fecha_inicio'])); ?></div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="text-dark fw-bold small"><i
                                                            class="far fa-calendar-check text-success opacity-75 me-1"></i>
                                                        <?php echo date('d M Y', strtotime($row['fecha_fin'])); ?></div>
                                                    <div class="text-muted small"><i
                                                            class="far fa-clock opacity-50 me-1"></i>
                                                        <?php echo date('h:i A', strtotime($row['fecha_fin'])); ?></div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php
                                                    $estado = strtolower($row['estado']);
                                                    if ($estado == 'programado'): ?>
                                                        <span
                                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                                                            <i class="far fa-calendar me-1"></i> Programado
                                                        </span>
                                                    <?php elseif ($estado == 'en curso'): ?>
                                                        <span
                                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                            <i class="fas fa-play me-1"></i> En Curso
                                                        </span>
                                                    <?php elseif ($estado == 'finalizado'): ?>
                                                        <span
                                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                            <i class="fas fa-check-double me-1"></i> Finalizado
                                                        </span>
                                                    <?php else: ?>
                                                        <span
                                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                                            <i class="fas fa-ban me-1"></i> Cancelado
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle flex-nowrap">
                                                    <a href="editar_evento.php?id=<?php echo $row['evento_id']; ?>"
                                                        class="btn btn-sm btn-action btn-action-edit text-primary me-1"
                                                        title="Editar"
                                                        style="background: rgba(78, 115, 223, 0.1); border-radius: 6px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="eliminar_evento.php?id=<?php echo $row['evento_id']; ?>"
                                                        class="btn btn-sm btn-action btn-action-delete text-danger"
                                                        title="Eliminar"
                                                        style="background: rgba(231, 74, 59, 0.1); border-radius: 6px;"
                                                        onclick="return confirm('¿Está seguro de que desea eliminar este evento de forma definitiva?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- fin card -->
                </div> <!-- fin tab-pane lista -->

                <!-- VISTA CALENDARIO -->
                <div class="tab-pane fade" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 btn-print-cal">
                                <div class="d-flex align-items-center gap-2">
                                    <label for="fechaIrCalendario" class="fw-bold mb-0 text-muted"
                                        style="font-size: 0.85rem;"><i class="fas fa-search me-1"></i> Ir a
                                        fecha:</label>
                                    <input type="date" id="fechaIrCalendario"
                                        class="form-control form-control-sm border-0 shadow-sm rounded-pill px-3"
                                        style="background-color: #f8fafc; color: #1e293b; outline: none; width: 140px; box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;">
                                </div>
                                <button class="btn text-white fw-bold px-4 rounded-pill shadow-sm"
                                    style="background: linear-gradient(135deg, #1e3a8a, #172554);"
                                    onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> Imprimir Calendario
                                </button>
                            </div>

                            <!-- Encabezado Oculto para Impresión -->
                            <div id="print-header-cal" style="display: none; margin-bottom: 20px;">
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #555; padding-bottom: 10px; margin-bottom: 15px;">
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 80px; height: 80px; margin-right: 20px; flex-shrink: 0;">
                                            <img src="/ProyectoIglesia/img/logo.png"
                                                style="width: 100%; height: 100%; object-fit: contain;">
                                        </div>
                                        <div style="text-align: left;">
                                            <h3
                                                style="margin: 0; color: #1e293b; font-weight: bold; font-family: 'Segoe UI', Arial, sans-serif;">
                                                Asamblea de Dios Rey de Reyes</h3>
                                            <p style="margin: 0; font-size: 14px; color: #555;">Zaculeu Central, zona 9,
                                                Huehuetenango</p>
                                            <p style="margin: 0; font-size: 14px; color: #555;">Teléfono: Pendiente</p>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <h4
                                            style="margin: 0; font-weight: bold; color: #4e73df; text-transform: uppercase;">
                                            Actividades del Mes <span id="nombreMesPrint"></span></h4>
                                    </div>
                                </div>
                            </div>

                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div> <!-- fin tab-content -->
        </div>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/es.global.min.js"></script>
<script>
    $(document).ready(function () {
        const $searchInput = $('.search-input');

        $searchInput.on('input', function () {
            const clearBtn = $('#clearSearch');
            if ($(this).val().length > 0) {
                clearBtn.show();
            } else {
                clearBtn.hide();
            }

            const searchTerm = $(this).val().toLowerCase();
            $('.table-softwys tbody tr').each(function () {
                const $row = $(this);
                const nameAndLocation = $row.find('td:eq(0)').text().toLowerCase();
                $row.toggle(nameAndLocation.includes(searchTerm));
            });
        });

        $('#clearSearch').on('click', function() {
            $searchInput.val('');
            $(this).hide();
            $('.table-softwys tbody tr').show();
            $searchInput.focus();
        });
    });

    function initializePagination() {
        const rowsPerPage = 10;
        const table = document.querySelector('.table-softwys');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const pageCount = Math.ceil(rows.length / rowsPerPage);

        if (pageCount <= 1) return;

        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
        }

        if (!document.querySelector('.pagination-container')) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'd-flex justify-content-end mt-4 pagination-container';

            let html = '<ul class="pagination pagination-sm shadow-sm opacity-75">';
            for (let i = 1; i <= pageCount; i++) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); window.changeListPage(${i})">${i}</a></li>`;
            }
            html += '</ul>';
            paginationContainer.innerHTML = html;

            table.parentNode.appendChild(paginationContainer);
        }

        window.changeListPage = showPage;
        showPage(1);
    }

    document.addEventListener('DOMContentLoaded', initializePagination);

    // Inicializar FullCalendar
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            contentHeight: 650, // Hace el calendario más compacto verticalmente
            aspectRatio: 1.8,   // Mejora la proporción horizontal
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                list: 'Agenda'
            },
            events: '/ProyectoIglesia/_/obtener_calendario',
            dateClick: function (info) {
                // Redirigir a crear evento con la fecha seleccionada por defecto (si la web está preparada)
                window.location.href = 'CrearEvento.php?fecha=' + info.dateStr;
            },
            eventClick: function (info) {
                // Redirigir a editar evento
                window.location.href = 'editar_evento.php?id=' + info.event.id;
            },
            eventDidMount: function (info) {
                info.el.title = info.event.title + (info.event.extendedProps.lugar ? ' - ' + info.event.extendedProps.lugar : '');
            },
            datesSet: function (info) {
                // Actualizar el título del mes en el documento de impresión automáticamente cada vez que se avanza/retrocede el calendario
                if (document.getElementById('nombreMesPrint')) {
                    document.getElementById('nombreMesPrint').innerText = info.view.title;
                }
            }
        });

        // Event listener para el selector rápido de fecha superior
        var fechaIrInput = document.getElementById('fechaIrCalendario');
        if (fechaIrInput) {
            fechaIrInput.addEventListener('change', function() {
                if (this.value) {
                  calendar.gotoDate(this.value);
                }
            });
        }

        // Asegurarse de que el calendario se dibuje bien cuando se cambie la pestaña a ser visible
        var calTab = document.getElementById('calendario-tab');
        calTab.addEventListener('shown.bs.tab', function () {
            calendar.render();
        });
    });
</script>

<?php
mysqli_free_result($result);
mysqli_close($conn);
include 'footer.php';
?>