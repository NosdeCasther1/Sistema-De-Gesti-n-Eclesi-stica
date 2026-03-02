<?php
include 'header.php';

// Incluir conexión a la base de datos
require_once __DIR__ . '../conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    if (empty($id)) {
        // Insertar famliar
        $sql = "INSERT INTO familias (nombre, descripcion, estado) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $descripcion, $estado);
    } else {
        // Actualizar Familiar
        $sql = "UPDATE familias SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $descripcion, $estado, $id);
    }

    $stmt->execute();
    $stmt->close();
}

// Eliminar Familia
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM familias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}


$sql = "SELECT * FROM familias";
$result = $conn->query($sql);
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
                    <h1 class="h2 text-dark font-weight-bold">Gestión de Familias</h1>
                    <p class="text-muted">Administra los núcleos familiares y grupos de la congregación.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        data-bs-toggle="modal" data-bs-target="#familiaModal" onclick="clearFamiliaModal()">
                        <i class="fas fa-plus me-2"></i> Agregar Familia
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-body p-4">
                    <!-- Buscador Unificado -->
                    <div class="row mb-4 bg-light p-3 rounded align-items-end" style="border: 1px solid #f0f0f0;">
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold mb-1"><i
                                    class="fas fa-search me-1"></i>Buscador Rápido</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i
                                        class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por Nombre de Familia..."
                                    style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Resultados -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Familia</th>
                                    <th style="width: 40%;">Descripción</th>
                                    <th style="width: 15%; text-align: center;">Estado</th>
                                    <th style="width: 10%; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $result->fetch_assoc()):
                                    // Generador dinámico de color de avatar
                                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
                                    $initial = strtoupper(substr($row['nombre'], 0, 1));
                                    $avatarColor = $colors[crc32($row['id']) % count($colors)];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                                                    style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">
                                                        <?php echo htmlspecialchars($row['nombre']); ?>
                                                    </div>
                                                    <div class="small text-muted"><i
                                                            class="fas fa-hashtag fa-sm opacity-50 me-1"></i>Id. Familia:
                                                        <?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted align-middle">
                                            <div class="text-truncate" style="max-width: 350px;"
                                                title="<?php echo htmlspecialchars($row['descripcion']); ?>">
                                                <?php echo htmlspecialchars($row['descripcion']); ?>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if (strtolower($row['estado']) == 'activo' || strtolower($row['estado']) == 'activa'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Activo
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-ban me-1"></i> Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-action btn-action-edit text-primary me-2"
                                                title="Editar"
                                                style="background: rgba(78, 115, 223, 0.1); border-radius: 6px;"
                                                onclick="editFamilia(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['descripcion'], ENT_QUOTES); ?>', '<?php echo $row['estado']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-action btn-action-delete text-danger"
                                                title="Eliminar"
                                                style="background: rgba(231, 74, 59, 0.1); border-radius: 6px;"
                                                onclick="deleteFamilia(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Principal Familias -->
<div class="modal fade" id="familiaModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fas fa-users text-primary me-2"></i>Registrar
                    Nueva Familia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="familiaForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-body px-4 pb-4">
                    <input type="hidden" id="familiaId" name="id">

                    <div class="mb-4">
                        <label for="nombre" class="form-label fw-bold text-muted small text-uppercase">Nombre del Grupo
                            Familiar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-home text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="nombre" name="nombre"
                                placeholder="Ej. Familia López García" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-bold text-muted small text-uppercase">Breve
                            Descripción o Residencia</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-map-marker-alt text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="descripcion"
                                name="descripcion" placeholder="Ej. Sector B, Zona 10..." required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado de
                            Actividad</label>
                        <select class="form-select form-select-lg" id="estado" name="estado"
                            style="font-size: 1rem; border-radius: 8px;" required>
                            <option value="">Seleccione el estado...</option>
                            <option value="Activo">Activa (Congregándose)</option>
                            <option value="Inactivo">Inactiva (Sin asistencia)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom px-4 pb-4">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal"
                        style="border-radius: 8px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;"><i
                            class="fas fa-save me-2"></i>Guardar Familia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function clearFamiliaModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-users text-primary me-2"></i>Registrar Nueva Familia';
        document.getElementById('familiaId').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('estado').value = '';
    }

    function editFamilia(id, nombre, descripcion, estado) {
        // Cambiar el título del modal a "Editar Familia"
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit text-primary me-2"></i>Editar Familia';

        // Llenar el formulario con los datos recibidos
        document.getElementById('familiaId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion;
        
        // Ajustar el valor del estado si viene capitalizado de forma diferente
        let estadoSelect = document.getElementById('estado');
        let optionFound = false;
        for (let i = 0; i < estadoSelect.options.length; i++) {
            if (estadoSelect.options[i].value.toLowerCase() === estado.toLowerCase()) {
                estadoSelect.selectedIndex = i;
                optionFound = true;
                break;
            }
        }
        if(!optionFound) {
             estadoSelect.value = estado;
        }

        // Mostrar el modal
        var modal = new bootstrap.Modal(document.getElementById('familiaModal'));
        modal.show();
    }

    function deleteFamilia(id) {
        if (confirm('¿Estás seguro de que quieres eliminar esta familia?')) {
            window.location.href = '?delete=' + id;
        }
    }

    $(document).ready(function () {
        // Obtener el campo de búsqueda
        const $searchInput = $('input[placeholder="Buscar por Nombre de Familia..."]');

        // Función para filtrar las filas de la tabla
        $searchInput.on('keyup', function () {
            const searchTerm = $(this).val().toLowerCase();

            $('table tbody tr').each(function () {
                const $row = $(this);
                // Obtener el texto de la columna nombre (la primera)
                const nombre = $row.find('td:eq(0)').text().toLowerCase();
                // Mostrar u ocultar la fila según el resultado
                $row.toggle(nombre.includes(searchTerm));
            });
        });

        // Agregar función para limpiar la búsqueda
        $searchInput.on('search', function () {
            if ($(this).val() === '') {
                $('table tbody tr').show();
            }
        });
    });

    function initializePagination() {
        const rowsPerPage = 10;
        const table = document.querySelector('.table-softwys');
        if(!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const pageCount = Math.ceil(rows.length / rowsPerPage);
        
        if (pageCount <= 1) return; // No paginar si solo hay 1 página

        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
        }

        // Crear controles de paginación si no existen
        if (!document.querySelector('.pagination-container')) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'd-flex justify-content-end mt-4 pagination-container';
            
            let html = '<ul class="pagination pagination-sm shadow-sm opacity-75">';
            for(let i=1; i<=pageCount; i++) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); window.changeListPage(${i})">${i}</a></li>`;
            }
            html += '</ul>';
            paginationContainer.innerHTML = html;

            table.parentNode.appendChild(paginationContainer);
        }

        window.changeListPage = showPage;
        // Mostrar la primera página
        showPage(1);
    }

    document.addEventListener('DOMContentLoaded', initializePagination);
</script>

<?php $conn->close(); ?>

<?php
require_once __DIR__ . '/footer.php';
?>
</body>

</html>