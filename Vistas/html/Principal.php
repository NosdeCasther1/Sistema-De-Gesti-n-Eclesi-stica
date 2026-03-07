<?php
// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener total de miembros
$queryMiembros = "SELECT COUNT(*) as total FROM miembros WHERE estado = 'Activo'";
$resultMiembros = mysqli_query($conn, $queryMiembros);

if ($resultMiembros) {
    $totalMiembros = mysqli_fetch_assoc($resultMiembros)['total'];
} else {
    // Manejo del error
    die("Error al realizar la consulta: " . mysqli_error($conn));
}

// Obtener total de egresos
$queryTotalEgresos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos";
$resultTotalEgresos = mysqli_query($conn, $queryTotalEgresos);
$totalEgresos = mysqli_fetch_assoc($resultTotalEgresos)['total'];

// Obtener total de ingresos (diezmos + ofrendas)
$queryTotalIngresos = "SELECT (
    COALESCE((SELECT SUM(monto) FROM diezmos ), 0) +
    COALESCE((SELECT SUM(monto) FROM ofrendas ), 0)
) as total";
$resultTotalIngresos = mysqli_query($conn, $queryTotalIngresos);
$totalIngresos = mysqli_fetch_assoc($resultTotalIngresos)['total'];

// Obtener últimos 5 ingresos combinando diezmos y ofrendas
$queryUltimosIngresos = "
    (SELECT d.fecha, d.monto, 'DIEZMO' as tipo, m.nombres, d.referencia, d.modo_pago, m.miembro_id
    FROM diezmos d
    INNER JOIN miembros m ON d.miembro = m.miembro_id)
    UNION ALL
    (SELECT o.fecha, o.monto, o.categoria as tipo, m.nombres, o.referencia, o.modo_pago, m.miembro_id
    FROM ofrendas o
    INNER JOIN miembros m ON o.miembro_id = m.miembro_id)
    ORDER BY fecha DESC 
    LIMIT 5";

$resultUltimosIngresos = mysqli_query($conn, $queryUltimosIngresos);
$ultimosIngresos = [];

while ($row = mysqli_fetch_assoc($resultUltimosIngresos)) {
    // Obtener el nombre del miembro completo
    $queryMiembro = "SELECT CONCAT(nombres, ' ', apellidos) as nombre_completo 
                     FROM miembros 
                     WHERE miembro_id = " . $row['miembro_id'];
    $resultMiembro = mysqli_query($conn, $queryMiembro);
    $nombreCompleto = $row['nombres']; // Default fallback
    if ($resultMiembro && $miembro = mysqli_fetch_assoc($resultMiembro)) {
        $nombreCompleto = $miembro['nombre_completo'];
    }

    $ultimosIngresos[] = [
        'fecha' => date('d/m/Y', strtotime($row['fecha'])),
        'monto' => $row['monto'],
        'tipo' => $row['tipo'],
        'miembro' => $nombreCompleto,
        'miembro_id' => $row['miembro_id']
    ];
}


// Obtener últimos 5 egresos
$queryUltimosEgresos = "SELECT fecha, monto, referencia FROM gastos ORDER BY fecha DESC LIMIT 5";
$resultUltimosEgresos = mysqli_query($conn, $queryUltimosEgresos);
$ultimosEgresos = [];
while ($row = mysqli_fetch_assoc($resultUltimosEgresos)) {
    $ultimosEgresos[] = [
        'fecha' => date('d/m/Y', strtotime($row['fecha'])),
        'monto' => $row['monto'],
        'referencia' => $row['referencia']
    ];
}

// Obtener estadísticas anuales
$anioActual = date('Y');
$estadisticasAnuales = [];
$meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

// Consulta para ingresos por mes (combinando diezmos y ofrendas)
$queryIngresosMensuales = "
    SELECT mes, SUM(total) as total
    FROM (
        SELECT MONTH(fecha) as mes, COALESCE(SUM(monto), 0) as total
        FROM diezmos
        WHERE YEAR(fecha) = ?
        GROUP BY MONTH(fecha)
        
        UNION ALL
        
        SELECT MONTH(fecha) as mes, COALESCE(SUM(monto), 0) as total
        FROM ofrendas
        WHERE YEAR(fecha) = ?
        GROUP BY MONTH(fecha)
    ) as ingresos_combinados
    GROUP BY mes
    ORDER BY mes
";

$stmtIngresos = mysqli_prepare($conn, $queryIngresosMensuales);
mysqli_stmt_bind_param($stmtIngresos, "ii", $anioActual, $anioActual); // Dos parámetros, uno para cada subconsulta
mysqli_stmt_execute($stmtIngresos);
$resultIngresos = mysqli_stmt_get_result($stmtIngresos);

// Consulta para egresos por mes
$queryEgresosMensuales = "
    SELECT MONTH(fecha) as mes, 
           COALESCE(SUM(monto), 0) as total
    FROM gastos 
    WHERE YEAR(fecha) = ?
    GROUP BY MONTH(fecha)
";
$stmtEgresos = mysqli_prepare($conn, $queryEgresosMensuales);
mysqli_stmt_bind_param($stmtEgresos, "i", $anioActual);
mysqli_stmt_execute($stmtEgresos);
$resultEgresos = mysqli_stmt_get_result($stmtEgresos);

// Inicializar array de estadísticas
foreach ($meses as $index => $mes) {
    $estadisticasAnuales[$mes] = ['ingreso' => 0, 'egreso' => 0];
}

// Llenar datos de ingresos
while ($row = mysqli_fetch_assoc($resultIngresos)) {
    $mes = $meses[$row['mes'] - 1];
    $estadisticasAnuales[$mes]['ingreso'] = $row['total'];
}

// Llenar datos de egresos
while ($row = mysqli_fetch_assoc($resultEgresos)) {
    $mes = $meses[$row['mes'] - 1];
    $estadisticasAnuales[$mes]['egreso'] = $row['total'];
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Principal - AD Rey de Reyes</title>
    <!-- Incluir librerías comunes a través del header o directamente -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            border-radius: 16px;
            border: none;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background: #ffffff;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        }

        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .chart-container-card {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .card-header-premium {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="wrapper">
        <!-- Barra lateral (menú) -->
        <?php require_once 'sidebar.php'; ?>

        <!-- Contenido principal -->
        <main class="main-content" style="background-color: #f8f9fc;">
            <div class="container-fluid py-4 px-4">

                <!-- Cabecera de Bienvenida -->
                <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 text-dark font-weight-bold mb-1">
                            👋 Bienvenido al Panel Principal
                        </h1>
                        <p class="text-muted mb-0">Resumen financiero y estadístico general de la iglesia "AD Rey de
                            Reyes".</p>
                    </div>
                    <div class="d-none d-md-flex align-items-center bg-white px-4 py-2 rounded-pill shadow-sm">
                        <i class="far fa-calendar-alt text-primary me-2"></i>
                        <div class="text-secondary small fw-bold">AÑO EN CURSO: <span
                                class="text-dark fs-6 ms-1"><?php echo $anioActual; ?></span></div>
                    </div>
                </div>

                <!-- Resumen de estadísticas (Tarjetas) -->
                <div class="row mb-4 g-4">
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm h-100">
                            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1"
                                        style="letter-spacing: 0.5px;">Comunidad Activa</p>
                                    <h2 class="fw-bold text-dark mb-0"><?php echo number_format($totalMiembros); ?>
                                        <span class="fs-6 text-muted fw-normal">miembros</span></h2>
                                </div>
                                <div class="icon-box bg-success bg-opacity-10 text-success shadow-sm">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm h-100 border-start border-4 border-primary">
                            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1"
                                        style="letter-spacing: 0.5px;">Ingresos Anuales</p>
                                    <h2 class="fw-bold text-dark mb-0 h3">Q
                                        <?php echo number_format($totalIngresos, 2); ?></h2>
                                </div>
                                <div class="icon-box bg-primary bg-opacity-10 text-primary shadow-sm">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm h-100 border-start border-4 border-danger">
                            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1"
                                        style="letter-spacing: 0.5px;">Egresos Anuales</p>
                                    <h2 class="fw-bold text-dark mb-0 h3">Q
                                        <?php echo number_format($totalEgresos, 2); ?></h2>
                                </div>
                                <div class="icon-box bg-danger bg-opacity-10 text-danger shadow-sm">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Ingresos vs Egresos y Tablas -->
                <div class="row g-4 mb-4">
                    <!-- Columna Izquierda: Gráfico principal -->
                    <div class="col-xl-7 col-lg-6">
                        <div class="card chart-container-card shadow-sm h-100 bg-white">
                            <div class="card-header-premium d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark mb-0 fs-5"><i
                                        class="fas fa-chart-bar text-primary me-2 opacity-75"></i>Balance Financiero
                                    Mensual</h6>
                                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><i
                                        class="fas fa-calendar-check me-1"></i><?php echo $anioActual; ?></span>
                            </div>
                            <div class="card-body p-4">
                                <div style="position: relative; height: 380px; width: 100%;">
                                    <canvas id="ingresosEgresosChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Tablas de Últimos Movimientos -->
                    <div class="col-xl-5 col-lg-6 d-flex flex-column gap-4">

                        <!-- Últimos Ingresos -->
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-50">
                            <div class="card-header-premium bg-white d-flex align-items-center">
                                <div class="text-success bg-success bg-opacity-10 rounded-circle me-3 d-flex justify-content-center align-items-center flex-shrink-0"
                                    style="width: 36px; height: 36px;">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Actividad Reciente (Ingresos)</h6>
                                    <small class="text-muted">Últimos 5 registros de ofrendas/diezmos</small>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-softwys table-hover align-middle mb-0 w-100">
                                        <thead class="bg-light d-none">
                                            <tr>
                                                <th>M</th>
                                                <th>Categoría</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($ultimosIngresos)): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                                                        <p class="mb-0">No hay ingresos recientes.</p>
                                                    </td>
                                                </tr>
                                            <?php else:
                                                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                                foreach ($ultimosIngresos as $ingreso):
                                                    $initial = strtoupper(substr($ingreso['miembro'], 0, 1));
                                                    $avatarColor = $colors[crc32($ingreso['miembro_id']) % count($colors)];
                                                    ?>
                                                    <tr>
                                                        <td class="ps-4" style="width: 60px;">
                                                            <div class="text-white rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                                                                style="width: 40px; height: 40px; font-weight: bold; font-size: 1.1rem; background-color: <?php echo $avatarColor; ?>;">
                                                                <?php echo $initial; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-dark text-truncate"
                                                                style="max-width: 180px;"
                                                                title="<?php echo htmlspecialchars($ingreso['miembro']); ?>">
                                                                <?php echo htmlspecialchars($ingreso['miembro']); ?>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                                <span
                                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2"
                                                                    style="font-size: 0.7rem;">
                                                                    <?php echo htmlspecialchars($ingreso['tipo']); ?>
                                                                </span>
                                                                <span class="text-muted small"><i
                                                                        class="far fa-calendar-alt me-1 opacity-50"></i><?php echo $ingreso['fecha']; ?></span>
                                                            </div>
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            <span class="fw-bold text-success fs-6"><i
                                                                    class="fas fa-plus mini-icon opacity-50"
                                                                    style="font-size: 0.6rem;"></i>
                                                                Q<?php echo number_format($ingreso['monto'], 2); ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Últimos Egresos -->
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-50">
                            <div class="card-header-premium bg-white d-flex align-items-center">
                                <div class="text-danger bg-danger bg-opacity-10 rounded-circle me-3 d-flex justify-content-center align-items-center flex-shrink-0"
                                    style="width: 36px; height: 36px;">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Últimos Gastos Registrados</h6>
                                    <small class="text-muted">Desglose de las 5 salidas recientes</small>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-softwys table-hover align-middle mb-0 w-100">
                                        <thead class="bg-light d-none">
                                            <tr>
                                                <th>Ref</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($ultimosEgresos)): ?>
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                                                        <p class="mb-0">No hay egresos recientes.</p>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($ultimosEgresos as $egreso): ?>
                                                    <tr>
                                                        <td class="ps-4">
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="bg-light rounded p-2 me-3 text-secondary opacity-75">
                                                                    <i class="fas fa-file-invoice-dollar fs-5"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-dark text-truncate"
                                                                        style="max-width: 220px;"
                                                                        title="<?php echo htmlspecialchars($egreso['referencia']); ?>">
                                                                        <?php echo htmlspecialchars($egreso['referencia']); ?>
                                                                    </div>
                                                                    <span class="text-muted small"><i
                                                                            class="far fa-calendar-alt me-1 opacity-50"></i><?php echo $egreso['fecha']; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-end pe-4 align-middle">
                                                            <span class="fw-bold text-danger fs-6"><i
                                                                    class="fas fa-minus mini-icon opacity-50"
                                                                    style="font-size: 0.6rem;"></i>
                                                                Q<?php echo number_format($egreso['monto'], 2); ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Configuración visual del gráfico de Ingresos vs Egresos para theme premium
        var ctx = document.getElementById('ingresosEgresosChart').getContext('2d');

        Chart.defaults.font.family = "'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
        Chart.defaults.color = '#8ea1b4';

        // Custom plugin to draw backgrounds (opcional para un toque ultra-premium)
        const customCanvasBackgroundColor = {
            id: 'customCanvasBackgroundColor',
            beforeDraw: (chart, args, options) => {
                const { ctx } = chart;
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = options.color || '#ffffff';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        };

        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($estadisticasAnuales)); ?>,
                datasets: [{
                    label: 'Ingresos Mensuales',
                    data: <?php echo json_encode(array_column($estadisticasAnuales, 'ingreso')); ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.9)',
                    hoverBackgroundColor: '#2e59d9',
                    borderColor: 'transparent',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }, {
                    label: 'Egresos Mensuales',
                    data: <?php echo json_encode(array_column($estadisticasAnuales, 'egreso')); ?>,
                    backgroundColor: 'rgba(231, 74, 59, 0.9)',
                    hoverBackgroundColor: '#e02d1b',
                    borderColor: 'transparent',
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 24,
                            font: {
                                size: 13,
                                weight: '600',
                                family: "'Segoe UI', Roboto, sans-serif"
                            },
                            color: '#5a5c69'
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgba(255,255,255,0.95)",
                        titleColor: '#3a3b45',
                        bodyColor: '#5a5c69',
                        titleFont: { size: 14, weight: 'bold', family: "'Segoe UI', Roboto, sans-serif" },
                        bodyFont: { size: 13, family: "'Segoe UI', Roboto, sans-serif" },
                        borderColor: '#eaecf4',
                        borderWidth: 1,
                        padding: { x: 15, y: 15 },
                        displayColors: true,
                        usePointStyle: true,
                        boxPadding: 6,
                        cornerRadius: 8,
                        titleAlign: 'center',
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Q' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 12,
                            font: { size: 12, weight: '500' },
                            color: '#858796'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "#eaecf4",
                            zeroLineColor: "#eaecf4",
                            drawBorder: false,
                            borderDash: [5, 5],
                            tickLength: 0
                        },
                        ticks: {
                            maxTicksLimit: 6,
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#858796',
                            callback: function (value, index, values) {
                                if (value >= 1000) {
                                    return 'Q' + (value / 1000) + 'k';
                                }
                                return 'Q' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>

    <?php require_once __DIR__ . '/footer.php'; ?>
</body>

</html>