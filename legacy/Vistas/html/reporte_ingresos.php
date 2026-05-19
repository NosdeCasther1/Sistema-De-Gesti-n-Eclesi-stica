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

// Obtener parámetros de búsqueda
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$tipo_ingreso = $_GET['tipo_ingreso'] ?? '';
$categoria = $_GET['categoria'] ?? '';

// Construir la consulta SQL base
$sql = "SELECT 
            'DIEZMO' as tipo,
            d.id,
            d.referencia,
            CONCAT(m.nombres, ' ', m.apellidos) as miembro,
            'DIEZMO' as tipo_ingreso,
            'DIEZMO' as categoria,
            d.monto,
            d.fecha,
            d.modo_pago,
            m.tel_celular,
            m.email
        FROM diezmos d
        JOIN miembros m ON d.miembro = m.miembro_id
        WHERE d.fecha BETWEEN ? AND ?
        
        UNION ALL
        
        SELECT 
            'OFRENDA' as tipo,
            o.ofrenda_id as id,
            o.referencia,
            CONCAT(m.nombres, ' ', m.apellidos) as miembro,
            'OFRENDA' as tipo_ingreso,
            o.categoria,
            o.monto,
            o.fecha,
            o.modo_pago,
            m.tel_celular,
            m.email
        FROM ofrendas o
        JOIN miembros m ON o.miembro_id = m.miembro_id
        WHERE o.fecha BETWEEN ? AND ?";

// Agregar filtros adicionales si se especifican
$params = [$fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin];
$types = "ssss";

if (!empty($tipo_ingreso)) {
    $sql .= " HAVING tipo_ingreso = ?";
    $params[] = $tipo_ingreso;
    $types .= "s";
}

if (!empty($categoria)) {
    $sql .= empty($tipo_ingreso) ? " HAVING" : " AND";
    $sql .= " categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

$sql .= " ORDER BY fecha DESC";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
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
                    <h1 class="h2 text-dark font-weight-bold">Reporte de Ingresos</h1>
                    <p class="text-muted">Consulta, filtra y exporta las entradas financieras de la iglesia.</p>
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
                            <label class="form-label text-muted small fw-bold mb-1">Tipo de Ingreso</label>
                            <select name="tipo_ingreso" class="form-select" style="border-radius: 8px;">
                                <option value="">Todos los tipos</option>
                                <option value="DIEZMO" <?php echo $tipo_ingreso == 'DIEZMO' ? 'selected' : ''; ?>>Diezmo
                                </option>
                                <option value="OFRENDA" <?php echo $tipo_ingreso == 'OFRENDA' ? 'selected' : ''; ?>>
                                    Ofrenda</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Categoría</label>
                            <select name="categoria" class="form-select" style="border-radius: 8px;">
                                <option value="">Todas las categorías</option>
                                <option value="DIEZMO" <?php echo $categoria == 'DIEZMO' ? 'selected' : ''; ?>>Diezmo
                                </option>
                                <option value="OFRENDA" <?php echo $categoria == 'OFRENDA' ? 'selected' : ''; ?>>Ofrenda
                                </option>
                                <option value="ENFERMOS" <?php echo $categoria == 'ENFERMOS' ? 'selected' : ''; ?>>
                                    Enfermos</option>
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
                                    <th>Ref.</th>
                                    <th>Fecha</th>
                                    <th>Miembro</th>
                                    <th class="text-center">Tipo Ingreso</th>
                                    <th class="text-center">Categoría</th>
                                    <th class="text-center">Modo Pago</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                while ($row = $result->fetch_assoc()):
                                    $total += $row['monto'];
                                    ?>
                                    <tr>
                                        <td class="text-muted fw-bold small"><i
                                                class="fas fa-receipt me-1"></i><?php echo $row['referencia']; ?></td>
                                        <td class="text-muted"><i
                                                class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y', strtotime($row['fecha'])); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                                                    style="width: 40px; height: 40px; font-weight: bold;">
                                                    <?php echo strtoupper(substr($row['miembro'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">
                                                        <?php echo htmlspecialchars($row['miembro']); ?>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <?php if (!empty($row['tel_celular']))
                                                            echo "<i class='fas fa-phone me-1'></i>" . htmlspecialchars($row['tel_celular']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['tipo_ingreso'] == 'DIEZMO'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                                    <i class="fas fa-hand-holding-usd me-1"></i>DIEZMO
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2">
                                                    <i class="fas fa-gift me-1"></i>OFRENDA
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                <?php echo htmlspecialchars($row['categoria']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $modoClass = '';
                                            switch ($row['modo_pago']) {
                                                case 'Efectivo':
                                                    $modoClass = 'text-success';
                                                    $icon = 'fa-money-bill-wave';
                                                    break;
                                                case 'Transferencia':
                                                    $modoClass = 'text-primary';
                                                    $icon = 'fa-exchange-alt';
                                                    break;
                                                case 'Cheque':
                                                    $modoClass = 'text-warning';
                                                    $icon = 'fa-money-check-alt';
                                                    break;
                                                default:
                                                    $modoClass = 'text-secondary';
                                                    $icon = 'fa-credit-card';
                                                    break;
                                            }
                                            ?>
                                            <div class="<?php echo $modoClass; ?> fw-bold small"><i
                                                    class="fas <?php echo $icon; ?> me-1"></i><?php echo $row['modo_pago']; ?>
                                            </div>
                                        </td>
                                        <td class="text-success fw-bold fs-6">Q
                                            <?php echo number_format($row['monto'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if ($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                                                <h5>No se encontraron registros</h5>
                                                <p>Ajuste los filtros de búsqueda para ver más resultados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold text-dark pe-4 fs-5">Total Recaudado en el
                                        Periodo:</td>
                                    <td colspan="2" class="text-success fw-bold fs-5">Q <span
                                            id="totalIngresos"><?php echo number_format($total, 2); ?></span></td>
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
        // Obtener valores de los filtros
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const tipoIngreso = document.querySelector('select[name="tipo_ingreso"]').value;
        const categoria = document.querySelector('select[name="categoria"]').value;

        // Construir URL con parámetros
        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            tipo_ingreso: tipoIngreso,
            categoria: categoria
        });

        // Redirigir a la página de exportación
        window.location.href = `<?php echo BASE_URL; ?>/exportar_pdf.php?${params.toString()}`;
    }
</script>

<?php
$stmt->close();
$conn->close();
?>

<?php include 'footer.php'; ?>