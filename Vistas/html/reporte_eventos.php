<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener parámetros de búsqueda
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d', strtotime('+30 days'));
$estado = $_GET['estado'] ?? '';
$lugar = $_GET['lugar'] ?? '';

// Construir la consulta SQL
$sql = "SELECT 
            evento_id,
            nombre_evento,
            descripcion,
            fecha_inicio,
            fecha_fin,
            lugar,
            estado,
            fecha_creacion,
            fecha_actualizacion
        FROM eventos 
        WHERE fecha_inicio BETWEEN ? AND ?";

$params = [$fecha_inicio, $fecha_fin];
$types = "ss";

if (!empty($estado)) {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}

if (!empty($lugar)) {
    $sql .= " AND lugar = ?";
    $params[] = $lugar;
    $types .= "s";
}

$sql .= " ORDER BY fecha_inicio ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
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
                    <h1 class="h2 text-dark font-weight-bold">Reporte de Eventos</h1>
                    <p class="text-muted">Consulta y exporta el historial y la planificación de las actividades de la
                        iglesia.</p>
                </div>
                <div>
                    <button class="btn btn-secondary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="exportarPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <!-- Filtros -->
                    <form method="GET" class="row g-3 align-items-end mb-4 bg-light p-3 rounded"
                        style="border: 1px solid #f0f0f0;">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold mb-1"><i
                                    class="far fa-calendar-alt me-1"></i>Rango de Fechas</label>
                            <input type="text" id="daterange" name="daterange" class="form-control"
                                style="border-radius: 8px;"
                                value="<?php echo date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)); ?>" />
                            <input type="hidden" name="fecha_inicio" id="fecha_inicio"
                                value="<?php echo $fecha_inicio; ?>">
                            <input type="hidden" name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Estado del Evento</label>
                            <select name="estado" class="form-select" style="border-radius: 8px;">
                                <option value="">Todos los estados</option>
                                <option value="Programado" <?php echo $estado == 'Programado' ? 'selected' : ''; ?>>
                                    Programado</option>
                                <option value="Cancelado" <?php echo $estado == 'Cancelado' ? 'selected' : ''; ?>>
                                    Cancelado</option>
                                <option value="Finalizado" <?php echo $estado == 'Finalizado' ? 'selected' : ''; ?>>
                                    Finalizado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Lugar</label>
                            <select name="lugar" class="form-select" style="border-radius: 8px;">
                                <option value="">Todos los lugares</option>
                                <option value="Salón Principal" <?php echo $lugar == 'Salón Principal' ? 'selected' : ''; ?>>Salón Principal</option>
                                <option value="Sala de Estudios" <?php echo $lugar == 'Sala de Estudios' ? 'selected' : ''; ?>>Sala de Estudios</option>
                                <option value="Iglesia" <?php echo $lugar == 'Iglesia' ? 'selected' : ''; ?>>Iglesia
                                    principal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 px-4 py-2"
                                style="border-radius: 8px; font-weight: 500;">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                        </div>
                    </form>

                    <!-- Tabla de resultados -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Detalles del Evento</th>
                                    <th style="width: 20%;">Inicio</th>
                                    <th style="width: 20%;">Fin</th>
                                    <th style="width: 15%; text-align: center;">Estado</th>
                                    <th style="width: 15%; text-align: center;">Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_eventos = 0;
                                while ($row = $result->fetch_assoc()):
                                    $total_eventos++;
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
                                                    <div class="small text-muted text-truncate" style="max-width: 250px;"
                                                        title="<?php echo htmlspecialchars($row['descripcion']); ?>">
                                                        <i
                                                            class="fas fa-map-marker-alt text-danger opacity-75 me-1"></i><?php echo htmlspecialchars($row['lugar']); ?>
                                                    </div>
                                                    <div class="small text-muted"><i
                                                            class="fas fa-hashtag fa-sm opacity-50 me-1"></i>Cód:
                                                        <?php echo str_pad($row['evento_id'], 4, '0', STR_PAD_LEFT); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="text-dark fw-bold small"><i
                                                    class="far fa-calendar-alt text-primary opacity-75 me-1"></i>
                                                <?php echo date('d M Y', strtotime($row['fecha_inicio'])); ?></div>
                                            <div class="text-muted small"><i class="far fa-clock opacity-50 me-1"></i>
                                                <?php echo date('h:i A', strtotime($row['fecha_inicio'])); ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="text-dark fw-bold small"><i
                                                    class="far fa-calendar-check text-success opacity-75 me-1"></i>
                                                <?php echo date('d M Y', strtotime($row['fecha_fin'])); ?></div>
                                            <div class="text-muted small"><i class="far fa-clock opacity-50 me-1"></i>
                                                <?php echo date('h:i A', strtotime($row['fecha_fin'])); ?></div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php
                                            $estadoStr = strtolower($row['estado']);
                                            if ($estadoStr == 'programado'): ?>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="far fa-calendar me-1"></i> Programado
                                                </span>
                                            <?php elseif ($estadoStr == 'en curso'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-play me-1"></i> En Curso
                                                </span>
                                            <?php elseif ($estadoStr == 'finalizado'): ?>
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
                                        <td class="text-center align-middle text-muted small">
                                            <?php echo date('d/m/Y', strtotime($row['fecha_creacion'])); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if ($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="far fa-calendar-times fa-3x mb-3 opacity-25"></i>
                                                <h5>No se encontraron eventos</h5>
                                                <p>Ajuste el rango de fechas u otros filtros para ver más resultados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-dark pe-4 fs-6">Total de eventos
                                        mostrados:</td>
                                    <td class="text-primary fw-bold fs-5 text-center"><?php echo $total_eventos; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(function () {
        $('#daterange').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                customRangeLabel: 'Rango personalizado',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ]
            }
        }, function (start, end) {
            $('#fecha_inicio').val(start.format('YYYY-MM-DD'));
            $('#fecha_fin').val(end.format('YYYY-MM-DD'));
        });
    });

    function exportarPDF() {
        const params = new URLSearchParams({
            fecha_inicio: document.getElementById('fecha_inicio').value,
            fecha_fin: document.getElementById('fecha_fin').value,
            estado: document.querySelector('select[name="estado"]').value,
            lugar: document.querySelector('select[name="lugar"]').value
        });
        window.location.href = `exportar_pdf_eventos.php?${params.toString()}`;
    }
</script>

<?php
$stmt->close();
$conn->close();
?>