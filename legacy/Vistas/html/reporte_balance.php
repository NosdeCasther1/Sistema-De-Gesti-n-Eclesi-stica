<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('reportes');
include 'header.php';
// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener parámetros de búsqueda (por defecto los últimos 30 días)
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

// --- 1. Total Diezmos ---
$query_diezmos = "SELECT SUM(monto) as total FROM diezmos WHERE fecha BETWEEN ? AND ?";
$stmt_diezmos = $conn->prepare($query_diezmos);
$stmt_diezmos->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_diezmos->execute();
$res_diezmos = $stmt_diezmos->get_result();
$total_diezmos = $res_diezmos->fetch_assoc()['total'] ?? 0;

// --- 2. Total Ofrendas ---
$query_ofrendas = "SELECT SUM(monto) as total FROM ofrendas WHERE fecha BETWEEN ? AND ?";
$stmt_ofrendas = $conn->prepare($query_ofrendas);
$stmt_ofrendas->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_ofrendas->execute();
$res_ofrendas = $stmt_ofrendas->get_result();
$total_ofrendas = $res_ofrendas->fetch_assoc()['total'] ?? 0;

// --- 3. Total Otros Ingresos ---
$query_otros = "SELECT SUM(monto) as total FROM otros_ingresos WHERE fecha BETWEEN ? AND ?";
$stmt_otros = $conn->prepare($query_otros);
$stmt_otros->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_otros->execute();
$res_otros = $stmt_otros->get_result();
$total_otros = $res_otros->fetch_assoc()['total'] ?? 0;

// --- 4. Total Gastos ---
$query_gastos = "SELECT SUM(monto) as total FROM gastos WHERE fecha BETWEEN ? AND ?";
$stmt_gastos = $conn->prepare($query_gastos);
$stmt_gastos->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_gastos->execute();
$res_gastos = $stmt_gastos->get_result();
$total_gastos = $res_gastos->fetch_assoc()['total'] ?? 0;

$total_ingresos = $total_diezmos + $total_ofrendas + $total_otros;
$saldo_neto = $total_ingresos - $total_gastos;

// --- Consultas para Gráficos: Agrupado por Mes ---
$query_mensual = "
    SELECT 
        DATE_FORMAT(fecha, '%Y-%m') as mes,
        SUM(CASE WHEN source = 'ingreso' THEN monto ELSE 0 END) as ingresos,
        SUM(CASE WHEN source = 'gasto' THEN monto ELSE 0 END) as gastos
    FROM (
        SELECT fecha, monto, 'ingreso' as source FROM diezmos WHERE fecha BETWEEN ? AND ?
        UNION ALL
        SELECT fecha, monto, 'ingreso' as source FROM ofrendas WHERE fecha BETWEEN ? AND ?
        UNION ALL
        SELECT fecha, monto, 'ingreso' as source FROM otros_ingresos WHERE fecha BETWEEN ? AND ?
        UNION ALL
        SELECT fecha, monto, 'gasto' as source FROM gastos WHERE fecha BETWEEN ? AND ?
    ) as movimientos
    GROUP BY DATE_FORMAT(fecha, '%Y-%m')
    ORDER BY mes ASC
";
$stmt_mensual = $conn->prepare($query_mensual);
$stmt_mensual->bind_param("ssssssss", $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin);
$stmt_mensual->execute();
$res_mensual = $stmt_mensual->get_result();

$meses = [];
$ingresos_mensuales = [];
$gastos_mensuales = [];

while ($row = $res_mensual->fetch_assoc()) {
    $meses[] = date('M Y', strtotime($row['mes'] . '-01'));
    $ingresos_mensuales[] = $row['ingresos'];
    $gastos_mensuales[] = $row['gastos'];
}

// --- Consulta Consolidada para Tabla: Flujo Diario ---
$sql_flujo = "
    SELECT id, fecha, referencia, monto, 'Diezmo' as categoria, 'Ingreso' as tipo_movimiento, 'Diezmo' as sub_tipo 
    FROM diezmos WHERE fecha BETWEEN ? AND ?
    UNION ALL
    SELECT ofrenda_id as id, fecha, referencia, monto, categoria, 'Ingreso' as tipo_movimiento, 'Ofrenda' as sub_tipo 
    FROM ofrendas WHERE fecha BETWEEN ? AND ?
    UNION ALL
    SELECT ingreso_id as id, fecha, referencia, monto, categoria, 'Ingreso' as tipo_movimiento, 'Otros' as sub_tipo 
    FROM otros_ingresos WHERE fecha BETWEEN ? AND ?
    UNION ALL
    SELECT id, fecha, referencia, monto, 
        (SELECT nombre FROM tipos_gasto WHERE id = gastos.tipo_gasto_id) as categoria, 
        'Egreso' as tipo_movimiento, 'Gasto' as sub_tipo 
    FROM gastos WHERE fecha BETWEEN ? AND ?
    ORDER BY fecha DESC, tipo_movimiento DESC
";
$stmt_flujo = $conn->prepare($sql_flujo);
$stmt_flujo->bind_param("ssssssss", $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin);
$stmt_flujo->execute();
$res_flujo = $stmt_flujo->get_result();
?>

<!-- Dependencias Globales del Reporte -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Balance Financiero</h1>
                    <p class="text-muted">Dashboard consolidado del Flujo de Caja (Ingresos vs Gastos).</p>
                </div>
            </div>

            <!-- Panel de Filtros -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <form method="GET" class="row align-items-end g-3 bg-light p-3 rounded"
                        style="border: 1px solid #f0f0f0;">
                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-bold mb-1"><i
                                    class="far fa-calendar-alt me-1"></i>Periodo de Gráficas y Reporte</label>
                            <input type="text" id="daterange" class="form-control" style="border-radius: 8px;"
                                value="<?php echo date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)); ?>" />
                            <input type="hidden" name="fecha_inicio" id="fecha_inicio"
                                value="<?php echo $fecha_inicio; ?>">
                            <input type="hidden" name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin; ?>">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 px-4 py-2"
                                style="border-radius: 8px; font-weight: 500;">
                                <i class="fas fa-search me-2"></i>Calcular Periodo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tarjetas de Resumen -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 py-2"
                        style="border-left: 4px solid #1cc88a !important; border-radius: 10px;">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Ingresos</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">Q
                                        <?php echo number_format($total_ingresos, 2); ?>
                                    </div>
                                    <small class="text-muted">Diezmos + Ofrendas + Otros</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hand-holding-usd fa-2x text-success opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 py-2"
                        style="border-left: 4px solid #e74a3b !important; border-radius: 10px;">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Egresos (Gastos)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">Q
                                        <?php echo number_format($total_gastos, 2); ?>
                                    </div>
                                    <small class="text-muted">Todos los gastos operativos</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-danger opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-12 mb-4">
                    <?php
                    $saldo_color_class = $saldo_neto >= 0 ? 'text-primary' : 'text-danger';
                    $border_color = $saldo_neto >= 0 ? '#4e73df' : '#e74a3b';
                    ?>
                    <div class="card border-0 shadow-sm h-100 py-2"
                        style="border-left: 4px solid <?php echo $border_color; ?> !important; border-radius: 10px; background-color: #f8f9fc;">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div
                                        class="text-xs font-weight-bold text-uppercase mb-1 <?php echo $saldo_color_class; ?>">
                                        Saldo Disponible (Utilidad)</div>
                                    <div class="h3 mb-0 font-weight-bold <?php echo $saldo_color_class; ?>">Q
                                        <?php echo number_format($saldo_neto, 2); ?>
                                    </div>
                                    <small class="text-muted">Flujo de caja neto para el periodo</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x <?php echo $saldo_color_class; ?> opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mb-4">
                <!-- Gráfico de Tendencia Mensual -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div
                            class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-dark"><i
                                    class="fas fa-chart-bar me-2 text-primary"></i>Comparativa de Ingresos vs Gastos
                                Mensual</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area" style="height: 300px;">
                                <canvas id="ingresosGastosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Donut de Fuentes de Ingreso -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div
                            class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-dark"><i
                                    class="fas fa-chart-pie me-2 text-success"></i>Distribución de Ingresos</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                                <canvas id="fuentesIngresoChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2"><i class="fas fa-circle text-primary"></i> Diezmos</span>
                                <span class="mr-2"><i class="fas fa-circle text-success"></i> Ofrendas</span>
                                <span class="mr-2"><i class="fas fa-circle text-info"></i> Otros</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Flujo Diario -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list me-2 text-primary"></i>Histórico de
                        Movimientos del Periodo</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Fecha</th>
                                    <th>Referencia</th>
                                    <th>Tipo de Movimiento</th>
                                    <th>Clasificación</th>
                                    <th class="text-end pe-4">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_flujo->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No se encontraron movimientos.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php while ($row = $res_flujo->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><i class="far fa-calendar-alt me-2"></i>
                                                <?php echo date('d/m/Y', strtotime($row['fecha'])); ?>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                <?php echo htmlspecialchars($row['referencia']); ?>
                                            </td>
                                            <td>
                                                <?php if ($row['tipo_movimiento'] == 'Ingreso'): ?>
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                                            class="fas fa-arrow-up me-1"></i>Ingreso</span>
                                                <?php else: ?>
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1"><i
                                                            class="fas fa-arrow-down me-1"></i>Egreso</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['sub_tipo'] . ($row['categoria'] ? ' - ' . $row['categoria'] : '')); ?>
                                            </td>
                                            <td
                                                class="text-end pe-4 fw-bold <?php echo $row['tipo_movimiento'] == 'Ingreso' ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo $row['tipo_movimiento'] == 'Ingreso' ? '+' : '-'; ?> Q
                                                <?php echo number_format($row['monto'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


<!-- Scripts -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Inicializar DateRangePicker
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
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            }
        }, function (start, end) {
            $('#fecha_inicio').val(start.format('YYYY-MM-DD'));
            $('#fecha_fin').val(end.format('YYYY-MM-DD'));
        });
    });

    // Gráfico de Barras: Comparativa Ingresos vs Gastos
    const ctxBar = document.getElementById('ingresosGastosChart').getContext('2d');
    const chartBar = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($meses); ?>,
            datasets: [
                {
                    label: 'Ingresos',
                    data: <?php echo json_encode($ingresos_mensuales); ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Gastos',
                    data: <?php echo json_encode($gastos_mensuales); ?>,
                    backgroundColor: 'rgba(231, 74, 59, 0.8)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'Q ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': Q ' + context.raw.toLocaleString(undefined, { minimumFractionDigits: 2 });
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Donut: Distribución de Ingresos
    const ctxPie = document.getElementById('fuentesIngresoChart').getContext('2d');
    const chartPie = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Diezmos', 'Ofrendas', 'Otros Ingresos'],
            datasets: [{
                data: [<?php echo $total_diezmos; ?>, <?php echo $total_ofrendas; ?>, <?php echo $total_otros; ?>],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.label + ': Q ' + context.raw.toLocaleString(undefined, { minimumFractionDigits: 2 });
                        }
                    }
                }
            }
        }
    });
</script>

<?php
$stmt_diezmos->close();
$stmt_ofrendas->close();
$stmt_otros->close();
$stmt_gastos->close();
$stmt_mensual->close();
$stmt_flujo->close();
$conn->close();
?>

<?php include 'footer.php'; ?>