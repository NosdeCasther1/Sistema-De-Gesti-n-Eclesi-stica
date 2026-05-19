<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener parámetros de búsqueda
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$tipo_gasto = $_GET['tipo_gasto'] ?? '';

// Construir la consulta SQL base
$sql = "SELECT 
            g.id,
            g.referencia,
            g.fecha,
            g.monto,
            g.tipo_gasto_id,
            g.descripcion,
            tg.nombre as tipo_gasto,
            g.created_at,
            g.updated_at
        FROM gastos g
        LEFT JOIN tipos_gasto tg ON g.tipo_gasto_id = tg.id
        WHERE g.fecha BETWEEN ? AND ?";

// Agregar filtros adicionales si se especifican
$params = [$fecha_inicio, $fecha_fin];
$types = "ss";

if (!empty($tipo_gasto)) {
    $sql .= " AND g.tipo_gasto_id = ?";
    $params[] = $tipo_gasto;
    $types .= "i";
}

$sql .= " ORDER BY g.fecha DESC";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Consulta para obtener los tipos de gasto
$tipos_gasto_sql = "SELECT id, nombre FROM tipos_gasto ORDER BY nombre";
$tipos_gasto_result = $conn->query($tipos_gasto_sql);
?>

<!-- Dependencias Globales del Reporte -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Reporte de Gastos</h1>
                    <p class="text-muted">Consulta, filtra y exporta las salidas financieras y pagos de la iglesia.</p>
                </div>
                <div>
                    <button class="btn btn-secondary px-4 py-2" style="border-radius: 8px; font-weight: 500;" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <!-- Filtros -->
                    <form method="GET" class="row g-3 align-items-end mb-4 bg-light p-3 rounded" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold mb-1"><i class="far fa-calendar-alt me-1"></i>Rango de Fechas</label>
                            <input type="text" id="daterange" name="daterange" class="form-control" style="border-radius: 8px;"
                                value="<?php echo date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)); ?>" />
                            <input type="hidden" name="fecha_inicio" id="fecha_inicio"
                                value="<?php echo $fecha_inicio; ?>">
                            <input type="hidden" name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold mb-1">Clasificación del Gasto</label>
                            <select name="tipo_gasto" class="form-select" style="border-radius: 8px;">
                                <option value="">Todos los tipos de gasto</option>
                                <?php while ($tipo = $tipos_gasto_result->fetch_assoc()): ?>
                                <option value="<?php echo $tipo['id']; ?>"
                                    <?php echo $tipo_gasto == $tipo['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                        </div>
                    </form>

                    <!-- Tabla de resultados -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Ref.</th>
                                    <th>Fecha de Gasto</th>
                                    <th class="text-center">Tipo / Categoría</th>
                                    <th>Descripción de la Salida</th>
                                    <th class="text-end">Monto (Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                while ($row = $result->fetch_assoc()): 
                                    $total += $row['monto'];
                                ?>
                                <tr>
                                    <td class="text-muted fw-bold small"><i class="fas fa-file-invoice-dollar me-1"></i><?php echo $row['referencia']; ?></td>
                                    <td class="text-muted">
                                        <div class="fw-bold text-dark"><i class="far fa-calendar-alt me-1 text-primary"></i><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></div>
                                        <div class="small" style="font-size: 0.75rem;">Reg: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="fas fa-tags me-1"></i><?php echo htmlspecialchars($row['tipo_gasto'] ?? 'No especificado'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 350px;" title="<?php echo htmlspecialchars($row['descripcion']); ?>">
                                            <?php echo htmlspecialchars($row['descripcion']); ?>
                                        </div>
                                    </td>
                                    <td class="text-end text-danger fw-bold fs-6">
                                        Q <?php echo number_format($row['monto'], 2); ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if($result->num_rows == 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-search-dollar fa-3x mb-3 opacity-25"></i>
                                            <h5>No se encontraron gastos</h5>
                                            <p>Ajuste el rango de fechas o busque un tipo de gasto distinto para ver más resultados.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-dark pe-4 fs-5">Total Egresos en el Periodo:</td>
                                    <td class="text-danger fw-bold fs-5 text-end">Q <?php echo number_format($total, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<!-- Scripts -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(function() {
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
    }, function(start, end) {
        $('#fecha_inicio').val(start.format('YYYY-MM-DD'));
        $('#fecha_fin').val(end.format('YYYY-MM-DD'));
    });
});

function exportarPDF() {
    const params = new URLSearchParams({
        fecha_inicio: document.getElementById('fecha_inicio').value,
        fecha_fin: document.getElementById('fecha_fin').value,
        tipo_gasto: document.querySelector('select[name="tipo_gasto"]').value
    });
    window.location.href = `<?php echo BASE_URL; ?>/exportar_pdf.php?${params.toString()}`;
}
</script>

    <?php 
    $stmt->close();
    $conn->close();
    ?>
</body>

</html>

<?php include 'footer.php'; ?>