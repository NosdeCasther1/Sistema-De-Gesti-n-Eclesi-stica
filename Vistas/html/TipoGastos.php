<?php
include 'header.php';

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Procesamiento de la eliminación de tipo de gasto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM tipos_gasto WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $mensaje = "Tipo de gasto eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar tipo de gasto: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_id = $_POST['tipo_id'] ?? null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    if ($tipo_id) {
        // Actualizar tipo de gasto existente
        $query = "UPDATE tipos_gasto SET nombre=?, descripcion=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $nombre, $descripcion, $tipo_id);
    } else {
        // Insertar nuevo tipo de gasto
        $query = "INSERT INTO tipos_gasto (nombre, descripcion) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $nombre, $descripcion);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = $tipo_id ? "Tipo de gasto actualizado exitosamente." : "Tipo de gasto agregado exitosamente.";
    } else {
        $mensaje = $tipo_id ? "Error al actualizar tipo de gasto: " : "Error al agregar tipo de gasto: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Consulta para obtener todos los tipos de gasto
$query = "SELECT * FROM tipos_gasto ORDER BY nombre";
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
                    <h1 class="h2 text-dark font-weight-bold">Tipos de Gasto</h1>
                    <p class="text-muted">Administre las categorías bajo las cuales se registrarán los gastos y egresos.
                    </p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="addTipoGasto()">
                        <i class="fas fa-plus me-2"></i>Nuevo Tipo
                    </button>
                </div>
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
                    <div class="row mb-4 bg-light p-3 rounded" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="form-label text-muted small fw-bold mb-1">Buscar Tipo de Gasto</label>
                            <div class="input-group"
                                style="box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-radius: 8px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" id="searchInput"
                                    placeholder="Nombre de categoría..." style="box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Nombre del Tipo</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 38px; height: 38px; background-color: #fee2e2; color: #ef4444; font-weight: bold; font-size: 1rem;">
                                                    <?php echo strtoupper(substr($row['nombre'], 0, 1)); ?>
                                                </div>
                                                <span
                                                    class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($row['nombre']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted text-wrap" style="max-width: 400px;">
                                            <?php echo htmlspecialchars($row['descripcion']); ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-action btn-action-edit me-1 px-3 py-1" title="Editar"
                                                    onclick='editTipoGasto(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-action btn-action-delete px-3 py-1" title="Eliminar"
                                                    onclick="deleteTipoGasto(<?php echo $row['id']; ?>)">
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

<!-- Modal para agregar/editar tipo de gasto -->
<div class="modal fade" id="tipoGastoModal" tabindex="-1" aria-labelledby="tipoGastoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" id="tipoGastoModalLabel"><i
                        class="fas fa-tags text-primary me-2"></i>Nuevo Tipo de Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="tipoGastoForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="tipo_id" name="tipo_id">
                    <div class="mb-4">
                        <label for="nombre" class="form-label text-muted fw-bold small">Nombre de la Categoría:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required
                            style="border-radius: 8px;">
                    </div>
                    <div class="mb-4">
                        <label for="descripcion" class="form-label text-muted fw-bold small">Descripción
                            (Opcional):</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                            style="border-radius: 8px; resize: none;"></textarea>
                    </div>
                    <div class="text-end border-top pt-3 mt-2">
                        <button type="button" class="btn btn-light px-4 py-2 me-2"
                            style="border-radius: 8px; font-weight: 500;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 py-2"
                            style="border-radius: 8px; font-weight: 500;"><i class="fas fa-save me-2"></i>Guardar
                            Tipo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function addTipoGasto() {
        document.getElementById('tipoGastoModalLabel').textContent = 'Nuevo Tipo de Gasto';
        document.getElementById('tipoGastoForm').reset();
        document.getElementById('tipo_id').value = '';
        var modal = new bootstrap.Modal(document.getElementById('tipoGastoModal'));
        modal.show();
    }

    function editTipoGasto(tipo) {
        document.getElementById('tipoGastoModalLabel').textContent = 'Editar Tipo de Gasto';
        document.getElementById('tipo_id').value = tipo.id;
        document.getElementById('nombre').value = tipo.nombre;
        document.getElementById('descripcion').value = tipo.descripcion;
        var modal = new bootstrap.Modal(document.getElementById('tipoGastoModal'));
        modal.show();
    }

    function deleteTipoGasto(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer. ¿Desea eliminar completamente este tipo de gasto?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?delete=' + id;
            }
        });
    }

    document.getElementById('searchInput').addEventListener('keyup', function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>

<?php include 'footer.php'; ?>