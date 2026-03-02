<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener parámetros de búsqueda
$estado = $_GET['estado'] ?? '';
$cargo = $_GET['cargo'] ?? '';
$familia = $_GET['familia'] ?? '';

// Construir la consulta SQL base
$sql = "SELECT 
            miembro_id,
            CONCAT(nombres, ' ', apellidos) as nombre_completo,
            familia,
            direccion,
            ciudad,
            tel_celular,
            tel_fijo,
            no_dpi,
            fecha_nacimiento,
            nivel_estudio,
            cargo,
            estado_civil,
            sexo,
            email,
            estado,
            fecha_ingreso
        FROM miembros
        WHERE 1=1";

// Agregar filtros adicionales si se especifican
$params = [];
$types = "";

if (!empty($estado)) {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}

if (!empty($cargo)) {
    $sql .= " AND cargo = ?";
    $params[] = $cargo;
    $types .= "s";
}

if (!empty($familia)) {
    $sql .= " AND familia = ?";
    $params[] = $familia;
    $types .= "s";
}

$sql .= " ORDER BY nombres ASC";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
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
                    <h1 class="h2 text-dark font-weight-bold">Reporte de Miembros</h1>
                    <p class="text-muted">Consulta y exporta el listado general de la congregación con filtros
                        avanzados.</p>
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
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Estado del Miembro</label>
                            <select name="estado" class="form-select" style="border-radius: 8px;">
                                <option value="">Todos los estados</option>
                                <option value="Activo" <?php echo $estado == 'Activo' ? 'selected' : ''; ?>>Activo
                                </option>
                                <option value="Inactivo" <?php echo $estado == 'Inactivo' ? 'selected' : ''; ?>>Inactivo
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Cargo a desempeñar</label>
                            <select name="cargo" class="form-select" style="border-radius: 8px;">
                                <option value="">Todos los cargos</option>
                                <option value="Tesorero" <?php echo $cargo == 'Tesorero' ? 'selected' : ''; ?>>Tesorero
                                </option>
                                <option value="Pastor" <?php echo $cargo == 'Pastor' ? 'selected' : ''; ?>>Pastor</option>
                                <option value="Diacono" <?php echo $cargo == 'Diacono' ? 'selected' : ''; ?>>Diácono
                                </option>
                                <option value="Miembro" <?php echo $cargo == 'Miembro' ? 'selected' : ''; ?>>Miembro
                                    Regular</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold mb-1">Familia / Apellido</label>
                            <div class="input-group"
                                style="box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-users text-muted"></i></span>
                                <input type="text" name="familia" class="form-control border-start-0"
                                    placeholder="Ej. Pérez García" style="box-shadow: none;"
                                    value="<?php echo htmlspecialchars($familia); ?>">
                            </div>
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
                                    <th>Cód. / DPI</th>
                                    <th>Miembro</th>
                                    <th>Grupo Familiar</th>
                                    <th class="text-center">Cargo / Rango</th>
                                    <th class="text-center">Estado Civil</th>
                                    <th class="text-center">Ingreso</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_miembros = 0;
                                while ($row = $result->fetch_assoc()):
                                    $total_miembros++;
                                    ?>
                                    <tr>
                                        <td class="text-muted fw-bold small">
                                            <div class="mb-1"><i class="fas fa-id-card me-1 text-primary"></i>ID:
                                                <?php echo $row['miembro_id']; ?></div>
                                            <div><i
                                                    class="far fa-id-badge me-1 text-secondary"></i><?php echo htmlspecialchars($row['no_dpi']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                // Dynamic colors for avatar based on first letter
                                                $initial = strtoupper(substr($row['nombre_completo'], 0, 1));
                                                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning text-dark', 'bg-danger'];
                                                $colorClass = $colors[ord($initial) % count($colors)];
                                                ?>
                                                <div class="avatar <?php echo $colorClass; ?> text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                                                    style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">
                                                        <?php echo htmlspecialchars($row['nombre_completo']); ?></div>
                                                    <div class="small text-muted mt-1">
                                                        <?php if (!empty($row['tel_celular']))
                                                            echo "<span class='me-2'><i class='fas fa-phone-alt me-1'></i>" . htmlspecialchars($row['tel_celular']) . "</span>"; ?>
                                                        <?php if (!empty($row['email']))
                                                            echo "<span><i class='far fa-envelope me-1'></i>" . htmlspecialchars($row['email']) . "</span>"; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                <i
                                                    class="fas fa-home me-1"></i><?php echo htmlspecialchars($row['familia']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['cargo'] == 'Pastor'): ?>
                                                <span class="badge bg-purple bg-opacity-10"
                                                    style="color: #6f42c1; border: 1px solid rgba(111, 66, 193, 0.3); border-radius: 20px; padding: 5px 12px;">
                                                    <i
                                                        class="fas fa-star me-1"></i><?php echo htmlspecialchars($row['cargo']); ?>
                                                </span>
                                            <?php elseif ($row['cargo'] == 'Tesorero' || $row['cargo'] == 'Diacono'): ?>
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                                    <i
                                                        class="fas fa-user-tie me-1"></i><?php echo htmlspecialchars($row['cargo']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="text-muted fw-bold small"><?php echo htmlspecialchars($row['cargo']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary fw-bold small"><i
                                                    class="fas fa-ring me-1 opacity-50"></i><?php echo htmlspecialchars($row['estado_civil']); ?></span>
                                        </td>
                                        <td class="text-center text-muted small fw-bold">
                                            <i class="far fa-calendar-alt text-primary opacity-75 d-block mb-1 fs-5"></i>
                                            <?php echo date('d M, Y', strtotime($row['fecha_ingreso'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (strtoupper($row['estado']) == 'ACTIVO'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                                    <i class="fas fa-circle ms-1 me-1"
                                                        style="font-size: 0.6em; vertical-align: middle;"></i> Activo
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">
                                                    <i class="fas fa-circle ms-1 me-1"
                                                        style="font-size: 0.6em; vertical-align: middle;"></i> Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if ($result->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-users-slash fa-3x mb-3 opacity-25"></i>
                                                <h5>No se encontraron miembros</h5>
                                                <p>Intente ajustar los filtros de búsqueda (estado, cargo o familia).</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold text-dark pe-4 fs-6">Número total de
                                        miembros en el reporte:</td>
                                    <td class="text-primary fw-bold fs-5 text-center"><?php echo $total_miembros; ?> <i
                                            class="fas fa-users ms-1 fs-6"></i></td>
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
<script>
    function exportarPDF() {
        const params = new URLSearchParams({
            estado: document.querySelector('select[name="estado"]').value,
            cargo: document.querySelector('select[name="cargo"]').value,
            familia: document.querySelector('input[name="familia"]').value
        });
        window.location.href = `exportar_pdf.php?${params.toString()}`;
    }
</script>
<?php
$stmt->close();
$conn->close();
?>
</body>

</html>

<?php include 'footer.php'; ?>