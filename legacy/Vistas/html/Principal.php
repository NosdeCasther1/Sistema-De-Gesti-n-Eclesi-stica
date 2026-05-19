<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('dashboard');

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
    $queryMiembro = "SELECT CONCAT(nombres, ' ', apellidos) as nombre_completo 
                     FROM miembros 
                     WHERE miembro_id = " . $row['miembro_id'];
    $resultMiembro = mysqli_query($conn, $queryMiembro);
    $nombreCompleto = $row['nombres']; 
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

// Estadísticas anuales
$anioActual = date('Y');
$estadisticasAnuales = [];
$meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

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
mysqli_stmt_bind_param($stmtIngresos, "ii", $anioActual, $anioActual);
mysqli_stmt_execute($stmtIngresos);
$resultIngresos = mysqli_stmt_get_result($stmtIngresos);

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

foreach ($meses as $mes) {
    $estadisticasAnuales[$mes] = ['ingreso' => 0, 'egreso' => 0];
}

while ($row = mysqli_fetch_assoc($resultIngresos)) {
    $mes = $meses[$row['mes'] - 1];
    $estadisticasAnuales[$mes]['ingreso'] = $row['total'];
}

while ($row = mysqli_fetch_assoc($resultEgresos)) {
    $mes = $meses[$row['mes'] - 1];
    $estadisticasAnuales[$mes]['egreso'] = $row['total'];
}

require_once 'header.php';
?>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">

            <!-- Cabecera de Bienvenida -->
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 kpi-value mb-1">👋 Bienvenido al Panel Principal</h1>
                    <p class="kpi-label mb-0">Resumen financiero y estadístico general de la iglesia "AD Rey de Reyes".</p>
                </div>
                <div class="d-none d-md-flex align-items-center bg-white px-4 py-2 rounded-pill shadow-sm">
                    <i class="far fa-calendar-alt text-primary me-2"></i>
                    <div class="text-secondary small fw-bold">AÑO EN CURSO: <span class="text-dark fs-6 ms-1"><?php echo $anioActual; ?></span></div>
                </div>
            </div>

            <!-- Resumen de estadísticas (Tarjetas) -->
            <div class="row mb-4 g-4">
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="kpi-label mb-1">Comunidad Activa</p>
                                <h2 class="kpi-value mb-0"><?php echo number_format($totalMiembros); ?> <span class="fs-6 kpi-label fw-normal">miembros</span></h2>
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
                                <p class="kpi-label mb-1">Ingresos Anuales</p>
                                <h2 class="kpi-value mb-0 h3">Q <?php echo number_format($totalIngresos, 2); ?></h2>
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
                                <p class="kpi-label mb-1">Egresos Anuales</p>
                                <h2 class="kpi-value mb-0 h3">Q <?php echo number_format($totalEgresos, 2); ?></h2>
                            </div>
                            <div class="icon-box bg-danger bg-opacity-10 text-danger shadow-sm">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico y Tablas -->
            <div class="row g-4 mb-4">
                <div class="col-xl-7 col-lg-6">
                    <div class="card chart-container-card shadow-sm h-100 bg-white">
                        <div class="card-header-premium d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0 fs-5"><i class="fas fa-chart-bar text-primary me-2 opacity-75"></i>Balance Financiero Mensual</h6>
                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><?php echo $anioActual; ?></span>
                        </div>
                        <div class="card-body p-4">
                            <div style="position: relative; height: 400px; width: 100%;">
                                <canvas id="ingresosEgresosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-6 d-flex flex-column gap-4">
                    <!-- Últimos Ingresos -->
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-50">
                        <div class="card-header-premium bg-white d-flex align-items-center">
                            <div class="text-success bg-success bg-opacity-10 rounded-circle me-3 d-flex justify-content-center align-items-center flex-shrink-0" style="width: 36px; height: 36px;">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Actividad Reciente</h6>
                                <small class="text-muted">Últimos 5 ingresos</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-softwys table-hover align-middle mb-0 w-100">
                                    <tbody>
                                        <?php if (empty($ultimosIngresos)): ?>
                                            <tr><td class="text-center py-4 text-muted">Sin ingresos recientes</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($ultimosIngresos as $ingreso): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($ingreso['miembro']); ?></div>
                                                        <span class="text-muted small"><?php echo $ingreso['fecha']; ?></span>
                                                    </td>
                                                    <td class="text-end pe-4 text-success fw-bold">+ Q<?php echo number_format($ingreso['monto'], 2); ?></td>
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
                            <div class="text-danger bg-danger bg-opacity-10 rounded-circle me-3 d-flex justify-content-center align-items-center flex-shrink-0" style="width: 36px; height: 36px;">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Últimos Gastos</h6>
                                <small class="text-muted">Últimos 5 egresos</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-softwys table-hover align-middle mb-0 w-100">
                                    <tbody>
                                        <?php if (empty($ultimosEgresos)): ?>
                                            <tr><td class="text-center py-4 text-muted">Sin gastos recientes</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($ultimosEgresos as $egreso): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="fw-bold text-dark text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($egreso['referencia']); ?></div>
                                                        <span class="text-muted small"><?php echo $egreso['fecha']; ?></span>
                                                    </td>
                                                    <td class="text-end pe-4 text-danger fw-bold">- Q<?php echo number_format($egreso['monto'], 2); ?></td>
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

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            var ctx = document.getElementById('ingresosEgresosChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($estadisticasAnuales)); ?>,
                    datasets: [{
                        label: 'Ingresos',
                        data: <?php echo json_encode(array_column($estadisticasAnuales, 'ingreso')); ?>,
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }, {
                        label: 'Egresos',
                        data: <?php echo json_encode(array_column($estadisticasAnuales, 'egreso')); ?>,
                        backgroundColor: '#f87171',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>

        <?php require_once 'footer.php'; ?>