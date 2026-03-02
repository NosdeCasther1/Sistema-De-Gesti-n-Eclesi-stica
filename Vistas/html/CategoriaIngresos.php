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

// Procesamiento de la eliminación de categoría
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM cat_ingresos WHERE ingresos_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $mensaje = "Categoría eliminada exitosamente.";
    } else {
        $mensaje = "Error al eliminar categoría: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ingresos_id = $_POST['ingresos_id'] ?? null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    if ($ingresos_id) {
        // Actualizar categoría existente
        $query = "UPDATE cat_ingresos SET nombre=?, descripcion=?, estado=? WHERE ingresos_id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nombre, $descripcion, $estado, $ingresos_id);
    } else {
        // Insertar nueva categoría
        $query = "INSERT INTO ingresos_id (nombre, descripcion, estado) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $descripcion, $estado);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = $ingresos_id ? "Categoría actualizada exitosamente." : "Categoría agregada exitosamente.";
    } else {
        $mensaje = $ingresos_id ? "Error al actualizar categoría: " : "Error al agregar categoría: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Consulta para obtener todas las categorías de ingresos de la base de datos
$query = "SELECT * FROM cat_ingresos";
$result = mysqli_query($conn, $query);
?>

<link rel="stylesheet" type="text/css" href="/ProyectoIglesia/styles/estilos.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/ProyectoIglesia/js/categoria_ingresos.js"></script>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Categorías de Ingresos</h1>
                    <p class="text-muted">Administra las etiquetas para clasificar las entradas de dinero.</p>
                </div>
                <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                    onclick="addCategoria()">
                    <i class="fas fa-plus me-2"></i>Nueva Categoría
                </button>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo strpos($mensaje, 'exitosamente') !== false ? 'success' : 'danger'; ?> alert-dismissible fade show shadow-sm"
                    role="alert">
                    <i
                        class="fas <?php echo strpos($mensaje, 'exitosamente') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group"
                                style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="buscarNombre"
                                    placeholder="Buscar categoría por nombre..." style="box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de Categoría</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td class="text-muted fw-bold">
                                            #<?php echo str_pad($row['ingresos_id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 35px; height: 35px; background-color: #e3effd; color: #0d6efd; font-weight: bold;">
                                                    <?php echo strtoupper(substr($row['nombre'], 0, 1)); ?>
                                                </div>
                                                <span
                                                    class="fw-bold text-dark"><?php echo htmlspecialchars($row['nombre']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted"><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                        <td class="text-center">
                                            <?php if (strtolower($row['estado']) == 'activo'): ?>
                                                <span
                                                    class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 border border-success border-opacity-25">Activo</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 border border-danger border-opacity-25"><?php echo htmlspecialchars($row['estado']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-action btn-action-edit me-1" title="Editar"
                                                    onclick='editCategoria(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-action-delete" title="Eliminar"
                                                    onclick="deleteCategoria(<?php echo $row['ingresos_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para agregar/editar categoría -->
<div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold text-dark" id="categoriaModalLabel"><i
                        class="fas fa-tag text-primary me-2"></i>Nueva Categoría de Ingreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="categoriaForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="ingresos_id" name="ingresos_id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label text-muted fw-bold small">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required
                            style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label text-muted fw-bold small">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"
                            style="border-radius: 8px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label text-muted fw-bold small">Estado</label>
                        <select class="form-select" id="estado" name="estado" style="border-radius: 8px;">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-light px-4 py-2 me-2"
                            style="border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 py-2"
                            style="border-radius: 8px; font-weight: 500;"><i
                                class="fas fa-save me-2"></i>Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function addCategoria() {
        document.getElementById('categoriaModalLabel').textContent = 'Nueva Categoría de Ingreso';
        document.getElementById('categoriaForm').reset();
        document.getElementById('ingresos_id').value = '';
        var modal = new bootstrap.Modal(document.getElementById('categoriaModal'));
        modal.show();
    }

    function editCategoria(categoria) {
        document.getElementById('categoriaModalLabel').textContent = 'Editar Categoría de Ingreso';
        document.getElementById('ingresos_id').value = categoria.ingresos_id;
        document.getElementById('nombre').value = categoria.nombre;
        document.getElementById('descripcion').value = categoria.descripcion;
        document.getElementById('estado').value = categoria.estado;
        var modal = new bootstrap.Modal(document.getElementById('categoriaModal'));
        modal.show();
    }

    function deleteCategoria(id) {
        if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
            window.location.href = '?delete=' + id;
        }
    }

    $(document).ready(function () {
        $("#buscarNombre").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
</body>

<?php include 'footer.php'; ?>