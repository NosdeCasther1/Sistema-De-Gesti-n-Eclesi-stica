<?php
require_once __DIR__ . '/../../Middleware/Permisos.php';
Permisos::verificar('celulas');
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

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid p-3 p-md-4 mb-5">
            <!-- Header del Módulo -->
            <div class="module-header">
                <div class="module-title-section">
                    <ul class="breadcrumb-custom mb-2">
                        <li class="breadcrumb-item-custom"><a href="/ProyectoIglesia/inicio">Inicio</a></li>
                        <li class="breadcrumb-item-custom active">Células Familiares</li>
                    </ul>
                    <h1 class="h2 text-dark font-weight-bold">Células Familiares</h1>
                    <p class="text-muted mb-0">Administra los grupos hogareños y sus líderes encargados.</p>
                </div>
                <div class="module-actions d-flex gap-2 align-items-center">
                    <button class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2" 
                        style="border-radius: 10px; font-weight: 600;" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf"></i> Reporte
                    </button>
                    <button class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2" 
                        style="border-radius: 10px; font-weight: 600;" onclick="addCelula()">
                        <i class="fas fa-plus"></i> Nueva Célula
                    </button>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card-module">
                <div class="card-header-custom">
                    <div class="search-bar-premium d-flex align-items-center w-100" style="max-width: 450px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control search-input" placeholder="Buscar por nombre, líder o dirección..." autocomplete="off">
                        <button class="btn btn-link text-muted p-2 border-0 clear-search" type="button" style="display: none;">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="table-responsive w-100">
                    <table class="table-custom w-100">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Célula Familiar</th>
                                <th style="width: 15%;">Líder</th>
                                <th style="width: 15%;">Anfitrión</th>
                                <th style="width: 15%;">Horario</th>
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
                                                style="width: 40px; height: 40px; font-weight: bold; background-color: <?php echo $avatarColor; ?>; border: 1px solid rgba(255,255,255,0.1);">
                                                <?php echo $initial; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nombre']); ?></div>
                                                <div class="small text-muted">ID: <?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted"><i class="fas fa-user-tie me-1"></i></span> <?php echo htmlspecialchars($row['lider_nombre']); ?></td>
                                    <td><span class="text-muted"><i class="fas fa-home me-1"></i></span> <?php echo htmlspecialchars($row['anfitrion']); ?></td>
                                    <td><span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2"><i class="far fa-clock me-1"></i> <?php echo htmlspecialchars($row['horario']); ?></span></td>
                                    <td class="text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($row['direccion']); ?></td>
                                    <td class="text-center">
                                        <?php if (strtolower($row['estado']) == 'activo'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-primary me-1" onclick='editCelula(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger" onclick="deleteCelula(<?php echo $row['id']; ?>)">
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

<!-- Modal para agregar/editar -->
<div class="modal fade" id="celulaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="celulaModalLabel">Nueva Célula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="celulaForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="celula_id" name="celula_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nombre de la Célula</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Líder</label>
                            <input type="text" class="form-control" id="lider_nombre" name="lider_nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Anfitrión</label>
                            <input type="text" class="form-control" id="anfitrion" name="anfitrion" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Día y Hora</label>
                            <input type="text" class="form-control" id="horario" name="horario" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Célula
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function addCelula() {
        document.getElementById('celulaModalLabel').textContent = 'Nueva Célula';
        document.getElementById('celulaForm').reset();
        document.getElementById('celula_id').value = '';
        new bootstrap.Modal(document.getElementById('celulaModal')).show();
    }

    function editCelula(celula) {
        document.getElementById('celulaModalLabel').textContent = 'Editar Célula';
        document.getElementById('celula_id').value = celula.id;
        document.getElementById('nombre').value = celula.nombre;
        document.getElementById('lider_nombre').value = celula.lider_nombre;
        document.getElementById('anfitrion').value = celula.anfitrion;
        document.getElementById('direccion').value = celula.direccion;
        document.getElementById('horario').value = celula.horario;
        document.getElementById('estado').value = celula.estado;
        new bootstrap.Modal(document.getElementById('celulaModal')).show();
    }

    function deleteCelula(id) {
        Swal.fire({
            title: '¿Eliminar célula?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?delete=' + id;
            }
        });
    }

    $(document).ready(function () {
        const $searchInput = $('.search-input');
        const $clearBtn = $('.clear-search');

        $searchInput.on('keyup', function () {
            const searchTerm = $(this).val().toLowerCase();
            $clearBtn.toggle(searchTerm.length > 0);
            
            $('.table-custom tbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().includes(searchTerm));
            });
        });

        $clearBtn.on('click', function() {
            $searchInput.val('').trigger('keyup').focus();
        });
    });

    function exportarPDF() {
        window.location.href = '<?php echo BASE_URL; ?>/exportar_pdf_celulas.php';
    }
</script>


    <?php require_once 'footer.php'; ?>