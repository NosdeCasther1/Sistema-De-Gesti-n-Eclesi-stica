<?php
require_once 'header.php';

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Config/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Procesamiento de la eliminación
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM celulas_familiares WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $mensaje = "Célula familiar eliminada exitosamente.";
    } else {
        $mensaje = "Error al eliminar la célula: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['celula_id'] ?? null;
    $nombre = $_POST['nombre'];
    $lider_nombre = $_POST['lider_nombre'];
    $anfitrion = $_POST['anfitrion'];
    $direccion = $_POST['direccion'];
    $horario = $_POST['horario'];
    $estado = $_POST['estado'];

    if (!empty($id)) {
        // Actualizar existente
        $query = "UPDATE celulas_familiares SET nombre=?, lider_nombre=?, anfitrion=?, direccion=?, horario=?, estado=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nombre, $lider_nombre, $anfitrion, $direccion, $horario, $estado, $id);
    } else {
        // Insertar nuevo
        $query = "INSERT INTO celulas_familiares (nombre, lider_nombre, anfitrion, direccion, horario, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nombre, $lider_nombre, $anfitrion, $direccion, $horario, $estado);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = !empty($id) ? "Célula actualizada exitosamente." : "Célula agregada exitosamente.";
    } else {
        $mensaje = !empty($id) ? "Error al actualizar la célula: " : "Error al agregar la célula: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Consulta para obtener todos los registros
$query = "SELECT * FROM celulas_familiares ORDER BY id DESC";
$result = mysqli_query($conn, $query);
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
                    <h1 class="h2 text-dark font-weight-bold">Células Familiares</h1>
                    <p class="text-muted">Administra los grupos hogareños, horarios y sus líderes encargados.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-file-alt me-2"></i> Reporte
                    </button>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="addCelula()">
                        <i class="fas fa-plus me-2"></i> Nueva Célula
                    </button>
                </div>
            </div>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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
                                <input type="text" class="form-control border-start-0 ps-0 search-input"
                                    placeholder="Buscar por Nombre, Líder o Dirección..."
                                    style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Célula Familiar</th>
                                    <th style="width: 15%;">Líder</th>
                                    <th style="width: 15%;">Anfitrión</th>
                                    <th style="width: 15%;">Día y Hora</th>
                                    <th style="width: 15%;">Dirección</th>
                                    <th style="width: 5%; text-align: center;">Estado</th>
                                    <th style="width: 10%; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                    $initial = strtoupper(substr($row['nombre'], 0, 1));
                                    $avatarColor = $colors[crc32($row['id']) % count($colors)];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm flex-shrink-0"
                                                    style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark fs-6 text-truncate"
                                                        style="max-width: 200px;"
                                                        title="<?php echo htmlspecialchars($row['nombre']); ?>">
                                                        <?php echo htmlspecialchars($row['nombre']); ?>
                                                    </div>
                                                    <div class="small text-muted"><i
                                                            class="fas fa-hashtag fa-sm opacity-50 me-1"></i>Ref:
                                                        <?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-tie me-2 opacity-50"></i>
                                                <span class="text-truncate" style="max-width: 120px;"
                                                    title="<?php echo htmlspecialchars($row['lider_nombre']); ?>">
                                                    <?php echo htmlspecialchars($row['lider_nombre']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-muted align-middle">
                                            <span class="text-truncate" style="max-width: 120px;"
                                                title="<?php echo htmlspecialchars($row['anfitrion']); ?>">
                                                <?php echo htmlspecialchars($row['anfitrion']); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted align-middle">
                                            <span class="badge bg-light text-dark border"><i
                                                    class="far fa-clock me-1 text-primary"></i>
                                                <?php echo htmlspecialchars($row['horario']); ?></span>
                                        </td>
                                        <td class="text-muted align-middle text-truncate" style="max-width: 150px;"
                                            title="<?php echo htmlspecialchars($row['direccion']); ?>">
                                            <i class="fas fa-map-marker-alt me-1 text-danger opacity-75"></i>
                                            <?php echo htmlspecialchars($row['direccion']); ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if (strtolower($row['estado']) == 'activo'): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Activo
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="fas fa-ban me-1"></i> Inactiva
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle flex-nowrap">
                                            <button class="btn btn-sm btn-action btn-action-edit text-primary me-1"
                                                title="Editar"
                                                style="background: rgba(78, 115, 223, 0.1); border-radius: 6px;"
                                                onclick='editCelula(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-action btn-action-delete text-danger"
                                                title="Eliminar"
                                                style="background: rgba(231, 74, 59, 0.1); border-radius: 6px;"
                                                onclick="deleteCelula(<?php echo $row['id']; ?>)">
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

<!-- Modal para agregar/editar -->
<div class="modal fade" id="celulaModal" tabindex="-1" aria-labelledby="celulaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                <h5 class="modal-title fw-bold" id="celulaModalLabel"><i class="fas fa-home text-primary me-2"></i>Nueva
                    Célula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form id="celulaForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="celula_id" name="celula_id">

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Datos Generales</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold text-muted small text-uppercase">Nombre de la
                                Célula</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-users text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="nombre" name="nombre"
                                    placeholder="Ej. Los Vencedores" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="lider_nombre" class="form-label fw-bold text-muted small text-uppercase">Nombre
                                del Líder</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-user-tie text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="lider_nombre"
                                    name="lider_nombre" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="anfitrion" class="form-label fw-bold text-muted small text-uppercase">Anfitrión
                                (Dueño de Casa)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="anfitrion"
                                    name="anfitrion" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="horario" class="form-label fw-bold text-muted small text-uppercase">Día y
                                Hora</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="far fa-calendar-alt text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="horario" name="horario"
                                    placeholder="Ej. Jueves 7:00 PM" required>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-4 border-bottom pb-2">Ubicación y Estado</h6>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="direccion" class="form-label fw-bold text-muted small text-uppercase">Dirección
                                Exacta</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-map-marker-alt text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="direccion"
                                    name="direccion" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado
                                Actual</label>
                            <select class="form-select form-select-lg" id="estado" name="estado"
                                style="font-size: 1rem; border-radius: 8px;" required>
                                <option value="Activo">Activo (Reuniones Regulares)</option>
                                <option value="Inactivo">Inactivo (Suspendida Temporalmente)</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 bg-light rounded-bottom px-4 pb-4 mt-4 mx-n4 mb-n4">
                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal"
                            style="border-radius: 8px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;"><i
                                class="fas fa-save me-2"></i>Guardar Célula</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addCelula() {
        document.getElementById('celulaModalLabel').innerHTML = '<i class="fas fa-home text-primary me-2"></i>Nueva Célula';
        document.getElementById('celulaForm').reset();
        document.getElementById('celula_id').value = '';
        var modal = new bootstrap.Modal(document.getElementById('celulaModal'));
        modal.show();
    }

    function editCelula(celula) {
        document.getElementById('celulaModalLabel').innerHTML = '<i class="fas fa-edit text-primary me-2"></i>Editar Célula';
        document.getElementById('celula_id').value = celula.id;
        document.getElementById('nombre').value = celula.nombre;
        document.getElementById('lider_nombre').value = celula.lider_nombre;
        document.getElementById('anfitrion').value = celula.anfitrion;
        document.getElementById('direccion').value = celula.direccion;
        document.getElementById('horario').value = celula.horario;

        let estadoSelect = document.getElementById('estado');
        for (let i = 0; i < estadoSelect.options.length; i++) {
            if (estadoSelect.options[i].value.toLowerCase() === celula.estado.toLowerCase()) {
                estadoSelect.selectedIndex = i;
                break;
            }
        }

        var modal = new bootstrap.Modal(document.getElementById('celulaModal'));
        modal.show();
    }

    function deleteCelula(id) {
        if (confirm('¿Estás seguro de que quieres eliminar esta célula? Esta acción no se puede deshacer.')) {
            window.location.href = '?delete=' + id;
        }
    }

    $(document).ready(function () {
        const $searchInput = $('.search-input');

        $searchInput.on('keyup', function () {
            const searchTerm = $(this).val().toLowerCase();

            $('.table-softwys tbody tr').each(function () {
                const $row = $(this);
                const textData = $row.text().toLowerCase();
                $row.toggle(textData.includes(searchTerm));
            });
        });

        $searchInput.on('search', function () {
            if ($(this).val() === '') {
                $('.table-softwys tbody tr').show();
            }
        });
    });

    function initializePagination() {
        const rowsPerPage = 10;
        const table = document.querySelector('.table-softwys');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const pageCount = Math.ceil(rows.length / rowsPerPage);

        if (pageCount <= 1) return;

        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
        }

        if (!document.querySelector('.pagination-container')) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'd-flex justify-content-end mt-4 pagination-container';

            let html = '<ul class="pagination pagination-sm shadow-sm opacity-75">';
            for (let i = 1; i <= pageCount; i++) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); window.changeListPage(${i})">${i}</a></li>`;
            }
            html += '</ul>';
            paginationContainer.innerHTML = html;

            table.parentNode.appendChild(paginationContainer);
        }

        window.changeListPage = showPage;
        showPage(1);
    }

    document.addEventListener('DOMContentLoaded', initializePagination);
</script>

<?php
require_once __DIR__ . '/footer.php';
?>
</body>

</html>