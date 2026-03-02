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

// Procesar el formulario cuando se envía (Crear/Editar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bien_id = $_POST['bien_id'] ?? null;
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $estado = $_POST['estado'];
    $ubicacion = $_POST['ubicacion'];
    $fecha_adquisicion = !empty($_POST['fecha_adquisicion']) ? $_POST['fecha_adquisicion'] : null;
    $valor_estimado = !empty($_POST['valor_estimado']) ? $_POST['valor_estimado'] : null;
    $observaciones = $_POST['observaciones'] ?? '';

    if ($bien_id) {
        // Actualizar bien existente
        $query = "UPDATE bienes_muebles SET codigo=?, nombre=?, categoria=?, estado=?, ubicacion=?, fecha_adquisicion=?, valor_estimado=?, observaciones=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssdsi", $codigo, $nombre, $categoria, $estado, $ubicacion, $fecha_adquisicion, $valor_estimado, $observaciones, $bien_id);
    } else {
        // Insertar nuevo bien
        $query = "INSERT INTO bienes_muebles (codigo, nombre, categoria, estado, ubicacion, fecha_adquisicion, valor_estimado, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssds", $codigo, $nombre, $categoria, $estado, $ubicacion, $fecha_adquisicion, $valor_estimado, $observaciones);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = $bien_id ? "Bien actualizado exitosamente." : "Bien registrado exitosamente.";
    } else {
        $mensaje = $bien_id ? "Error al actualizar bien: " . mysqli_error($conn) : "Error al registrar bien: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Procesamiento de la eliminación de bien
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($id !== false) {
        $query = "DELETE FROM bienes_muebles WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "Bien eliminado exitosamente.";
        } else {
            $mensaje = "Error al eliminar bien: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Consulta para obtener todos los bienes
$query = "SELECT * FROM bienes_muebles ORDER BY fecha_creacion DESC";
$result = mysqli_query($conn, $query);
?>

<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Inventario de Bienes</h1>
                    <p class="text-muted">Administre el registro y control de mobiliario y equipo.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary px-4 py-2 shadow-sm" style="border-radius: 8px; font-weight: 500;"
                        onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Reporte
                    </button>
                    <button class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 8px; font-weight: 500;"
                        onclick="addBien()">
                        <i class="fas fa-plus me-2"></i>Nuevo Bien
                    </button>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo strpos($mensaje, 'exitosamente') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show shadow-sm"
                    role="alert" style="border-radius: 10px;">
                    <i
                        class="fas <?php echo strpos($mensaje, 'exitosamente') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-body p-4">
                    <!-- Buscador Unificado -->
                    <div class="row mb-4 align-items-center bg-light p-3 rounded-3 border">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold mb-1"><i
                                    class="fas fa-search me-1"></i>Buscador Rápido</label>
                            <div class="input-group"
                                style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="searchInput"
                                    placeholder="Buscar por código, artículo, categoría o ubicación..."
                                    style="box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100" id="tabla-bienes">
                            <thead>
                                <tr>
                                    <th>Detalles del Artículo</th>
                                    <th>Categoría</th>
                                    <th><i class="fas fa-map-marker-alt me-1"></i>Ubicación</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_valor = 0;
                                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                while ($row = mysqli_fetch_assoc($result)):
                                    $total_valor += floatval($row['valor_estimado']);
                                    $initial = strtoupper(substr($row['nombre'], 0, 1));
                                    $avatarColor = $colors[crc32($row['id']) % count($colors)];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-white rounded d-flex justify-content-center align-items-center me-3 shadow-sm flex-shrink-0"
                                                    style="width: 48px; height: 48px; font-weight: bold; font-size: 1.4rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6 text-truncate"
                                                        style="max-width: 280px;"
                                                        title="<?php echo htmlspecialchars($row['nombre']); ?>">
                                                        <?php echo htmlspecialchars($row['nombre']); ?>
                                                    </div>
                                                    <div class="small text-muted d-flex gap-2 mt-1">
                                                        <span><i
                                                                class="fas fa-barcode opacity-50 me-1"></i><?php echo htmlspecialchars($row['codigo']); ?></span>
                                                        <?php if (!empty($row['valor_estimado'])): ?>
                                                            <span class="text-success fw-bold"><i
                                                                    class="fas fa-coins opacity-75 md-1"></i> Q
                                                                <?php echo number_format($row['valor_estimado'], 2); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="fw-bold text-secondary"><?php echo htmlspecialchars($row['categoria']); ?></span>
                                                <?php if (!empty($row['fecha_adquisicion'])): ?>
                                                    <span class="small text-muted"><i
                                                            class="far fa-calendar-alt me-1 opacity-50"></i>Adq:
                                                        <?php echo date('d/m/Y', strtotime($row['fecha_adquisicion'])); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            <div class="text-dark fw-bold small"><i
                                                    class="fas fa-map-pin text-primary opacity-50 me-1"></i><?php echo htmlspecialchars($row['ubicacion']); ?>
                                            </div>
                                        </td>

                                        <td class="text-center align-middle">
                                            <?php
                                            $estadoClass = '';
                                            $estadoIcon = '';
                                            switch (strtoupper($row['estado'])) {
                                                case 'NUEVO':
                                                case 'BUENO':
                                                case 'ÓPTIMO':
                                                    $estadoClass = 'bg-success text-success border-success';
                                                    $estadoIcon = 'fa-check-circle';
                                                    break;
                                                case 'REGULAR':
                                                case 'EN REPARACIÓN':
                                                    $estadoClass = 'bg-warning text-warning border-warning';
                                                    $estadoIcon = 'fa-tools';
                                                    break;
                                                case 'MALO':
                                                case 'OBSOLETO':
                                                case 'BAJA':
                                                    $estadoClass = 'bg-danger text-danger border-danger';
                                                    $estadoIcon = 'fa-times-circle';
                                                    break;
                                                default:
                                                    $estadoClass = 'bg-secondary text-secondary border-secondary';
                                                    $estadoIcon = 'fa-info-circle';
                                                    break;
                                            }
                                            ?>
                                            <span
                                                class="badge <?php echo $estadoClass; ?> bg-opacity-10 border border-opacity-25 rounded-pill px-3 py-2 shadow-sm">
                                                <i class="fas <?php echo $estadoIcon; ?> me-1"></i>
                                                <?php echo htmlspecialchars($row['estado']); ?>
                                            </span>
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button
                                                    class="btn btn-sm btn-action btn-action-edit text-primary bg-primary bg-opacity-10 border-0"
                                                    title="Editar"
                                                    onclick='editBien(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-action btn-action-delete text-danger bg-danger bg-opacity-10 border-0"
                                                    title="Eliminar" onclick="deleteBien(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                                <?php if (mysqli_num_rows($result) == 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                                <h5>Ingresa tu primer Bien Mueble</h5>
                                                <p>Mantén un control detallado del inventario creando tu primer registro.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-dark pe-4 fs-6">Valor Total Estimado
                                        del Inventario:</td>
                                    <td class="text-success fw-bold fs-5 text-center"><i
                                            class="fas fa-coins me-2 opacity-75"></i>Q
                                        <?php echo number_format($total_valor, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para agregar/editar bien -->
<div class="modal fade" id="bienModal" tabindex="-1" aria-labelledby="bienModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom-0 p-4">
                <h5 class="modal-title fw-bold text-dark" id="bienModalLabel">
                    <i class="fas fa-chair text-primary me-2"></i>Registrar Bien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 pt-3">
                <form id="bienForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="bien_id" name="bien_id">

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Identificación del Artículo</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label text-muted fw-bold small text-uppercase">Código de
                                Inventario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fas fa-barcode text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 ps-0 text-uppercase"
                                    id="codigo" name="codigo" required
                                    style="border-radius: 0 8px 8px 0; font-family: monospace; font-weight: bold; box-shadow: none;"
                                    placeholder="Ej. MOB-001">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria"
                                class="form-label text-muted fw-bold small text-uppercase">Categoría</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-tag text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0" id="categoria" name="categoria" required
                                    style="border-radius: 0 8px 8px 0;">
                                    <option value="" selected disabled>Elija una opción...</option>
                                    <option value="Mobiliario">Mobiliario</option>
                                    <option value="Equipo Electrónico">Equipo Electrónico</option>
                                    <option value="Instrumentos Musicales">Instrumentos Musicales</option>
                                    <option value="Audio y Sonido">Audio y Sonido</option>
                                    <option value="Herramientas">Herramientas</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="nombre" class="form-label text-muted fw-bold small text-uppercase">Nombre /
                            Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-align-left text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="nombre" name="nombre"
                                required placeholder="Ej. Silla de oficina ejecutiva negra"
                                style="border-radius: 0 8px 8px 0; box-shadow: none;">
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-2">2. Estado y Valoración</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label text-muted fw-bold small text-uppercase">Estado
                                Físico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-heartbeat text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0" id="estado" name="estado" required
                                    style="border-radius: 0 8px 8px 0;">
                                    <option value="" selected disabled>Elija una opción...</option>
                                    <option value="Nuevo">Nuevo</option>
                                    <option value="Bueno">Bueno</option>
                                    <option value="Regular">Regular</option>
                                    <option value="En Reparación">En Reparación</option>
                                    <option value="Malo">Malo / Para Baja</option>
                                    <option value="Óptimo">Óptimo</option>
                                    <option value="Obsoleto">Obsoleto</option>
                                    <option value="Baja">Baja</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ubicacion" class="form-label text-muted fw-bold small text-uppercase">Ubicación
                                Asignada</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-map-marker-alt text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="ubicacion"
                                    name="ubicacion" required placeholder="Ej. Salón Principal"
                                    style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_adquisicion"
                                class="form-label text-muted fw-bold small text-uppercase">Fecha Adquisición</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="far fa-calendar-alt text-muted"></i></span>
                                <input type="date" class="form-control border-start-0 ps-0" id="fecha_adquisicion"
                                    name="fecha_adquisicion" style="border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="valor_estimado" class="form-label text-muted fw-bold small text-uppercase">Valor
                                Estimado (Q)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success fw-bold border-end-0"><i
                                        class="fas fa-coins me-1"></i> Q</span>
                                <input type="number" step="0.01"
                                    class="form-control bg-light border-start-0 ps-1 fw-bold fs-5 text-success"
                                    id="valor_estimado" name="valor_estimado" placeholder="0.00"
                                    style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observaciones" class="form-label text-muted fw-bold small text-uppercase">Notas
                            Adicionales</label>
                        <textarea class="form-control bg-light" id="observaciones" name="observaciones" rows="2"
                            style="border-radius: 8px; resize: none; border-color: #e3e6f0;"
                            placeholder="Agregue cualquier detalle relevante sobre el artículo..."></textarea>
                    </div>

                    <div class="text-end border-top pt-4 mt-2">
                        <button type="button" class="btn btn-light px-4 py-2 me-2"
                            style="border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-5 py-2"
                            style="border-radius: 8px; font-weight: bold;"><i class="fas fa-save me-2"></i>Guardar
                            Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // Buscador Dinámico Unificado
        $('#searchInput').on('keyup', function () {
            const searchValue = $(this).val().toLowerCase();

            $('#tabla-bienes tbody tr').each(function () {
                // Obtenemos todo el texto de la fila para una búsqueda más global
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.indexOf(searchValue) > -1);
            });
        });
    });

    function deleteBien(id) {
        if (confirm('¿Estás seguro de que quieres eliminar este artículo del inventario?\nEsta acción es irreversible.')) {
            window.location.href = '?delete=' + id;
        }
    }

    function addBien() {
        document.getElementById('bienModalLabel').innerHTML = '<i class="fas fa-chair text-primary me-2"></i>Registrar Bien';
        document.getElementById('bienForm').reset();
        document.getElementById('bien_id').value = '';
        var modal = new bootstrap.Modal(document.getElementById('bienModal'));
        modal.show();
    }

    function editBien(bien) {
        document.getElementById('bienModalLabel').innerHTML = '<i class="fas fa-edit text-primary me-2"></i>Editar Registro de Bien';
        document.getElementById('bien_id').value = bien.id;
        document.getElementById('codigo').value = bien.codigo;
        document.getElementById('nombre').value = bien.nombre;
        document.getElementById('categoria').value = bien.categoria;
        document.getElementById('estado').value = bien.estado;
        document.getElementById('ubicacion').value = bien.ubicacion;
        document.getElementById('fecha_adquisicion').value = bien.fecha_adquisicion;
        document.getElementById('valor_estimado').value = bien.valor_estimado;
        document.getElementById('observaciones').value = bien.observaciones;
        var modal = new bootstrap.Modal(document.getElementById('bienModal'));
        modal.show();
    }
</script>

<?php include 'footer.php'; ?>
</body>

</html>