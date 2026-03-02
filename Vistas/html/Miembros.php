<?php
require_once 'header.php';
?>

<?php

// Incluir conexión a la base de datos
require_once __DIR__ . '/conexion.php';

// Llamar a la función getDBConnection para obtener la conexión
$conn = getDBConnection();

// Verificar la conexión
if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Procesamiento de la eliminación de miembro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM miembros WHERE miembro_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $mensaje = "Miembro eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar miembro: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $miembro_id = $_POST['miembro_id'] ?? null;
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];
    $familia = $_POST['familia'] ?? '';
    $tel_celular = $_POST['tel_celular'];
    $tel_fijo = $_POST['tel_fijo'];
    $no_dpi = $_POST['no_dpi'];
    $fecha_nacimiento = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_nacimiento'])));
    $nivel_estudio = $_POST['nivel_estudio'];
    $cargo = $_POST['cargo'] ?? '';
    $estado_civil = $_POST['estado_civil'] ?? '';
    $sexo = $_POST['sexo'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];

    if ($miembro_id) {
        // Actualizar miembro existente
        $query = "UPDATE miembros SET nombres=?, apellidos=?, direccion=?, ciudad=?, familia=?, tel_celular=?, tel_fijo=?, no_dpi=?, fecha_nacimiento=?, nivel_estudio=?, cargo=?, estado_civil=?, sexo=?, email=?, estado=? WHERE miembro_id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssssssssssi", $nombres, $apellidos, $direccion, $ciudad, $familia, $tel_celular, $tel_fijo, $no_dpi, $fecha_nacimiento, $nivel_estudio, $cargo, $estado_civil, $sexo, $email, $estado, $miembro_id);
    } else {
        // Insertar nuevo miembro
        $query = "INSERT INTO miembros (nombres, apellidos, direccion, ciudad, familia, tel_celular, tel_fijo, no_dpi, fecha_nacimiento, nivel_estudio, cargo, estado_civil, sexo, email, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssssssssss", $nombres, $apellidos, $direccion, $ciudad, $familia, $tel_celular, $tel_fijo, $no_dpi, $fecha_nacimiento, $nivel_estudio, $cargo, $estado_civil, $sexo, $email, $estado);
    }

    if (mysqli_stmt_execute($stmt)) {
        $mensaje = $miembro_id ? "Miembro actualizado exitosamente." : "Miembro agregado exitosamente.";
    } else {
        $mensaje = $miembro_id ? "Error al actualizar miembro: " : "Error al agregar miembro: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Consulta para obtener todos los miembros de la base de datos
$query = "SELECT * FROM miembros";
$result = mysqli_query($conn, $query);
?>

<!-- Luego, tu archivo JavaScript -->
<script src="/assets/js/miembros.js"></script>

<!-- Estructura principal de la página -->
<div class="wrapper">
    <!-- Barra lateral (menú) -->
    <?php require_once 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 text-dark font-weight-bold">Gestión de Miembros</h1>
                    <p class="text-muted">Administra los registros y datos de los miembros de la congregación.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-file-alt me-2"></i> Reporte
                    </button>
                    <button class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;"
                        onclick="addMiembro()">
                        <i class="fas fa-plus me-2"></i> Agregar Miembro
                    </button>
                </div>
            </div>

            <!-- Mostrar mensaje de éxito o error después de agregar/editar/eliminar un miembro -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <?php echo $mensaje; ?>
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
                                <input type="text" class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por Nombre, Apellido, DPI o Ciudad..."
                                    style="border-radius: 0 8px 8px 0; box-shadow: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla con scroll horizontal cuando sea necesario -->
                    <div class="table-responsive">
                        <table class="table-softwys table-hover w-100">

                            <!-- Tabla de miembros -->
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Miembro</th>
                                    <th style="width: 10%;">DPI</th>
                                    <th style="width: 12%;">Tel. Celular</th>
                                    <th style="width: 13%;">Ciudad</th>
                                    <th style="width: 15%;">Email</th>
                                    <th style="width: 5%; text-align: center;">Estado</th>
                                    <th style="width: 10%;">Registro</th>
                                    <th style="width: 10%; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                    // Generador dinámico de color de avatar
                                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];
                                    $initial = strtoupper(substr($row['nombres'], 0, 1));
                                    $avatarColor = $colors[crc32($row['miembro_id']) % count($colors)];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                                                    style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem; background-color: <?php echo $avatarColor; ?>;">
                                                    <?php echo $initial; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6 text-truncate"
                                                        style="max-width: 180px;"
                                                        title="<?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']); ?>">
                                                        <?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']); ?>
                                                    </div>
                                                    <div class="small text-muted"><i
                                                            class="fas fa-hashtag fa-sm opacity-50 me-1"></i>Id:
                                                        <?php echo str_pad($row['miembro_id'], 4, '0', STR_PAD_LEFT); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted align-middle"><?php echo htmlspecialchars($row['no_dpi']); ?>
                                        </td>
                                        <td class="text-muted align-middle">
                                            <?php echo htmlspecialchars($row['tel_celular']); ?></td>
                                        <td class="text-muted align-middle text-truncate" style="max-width: 120px;"
                                            title="<?php echo htmlspecialchars($row['ciudad']); ?>">
                                            <?php echo htmlspecialchars($row['ciudad']); ?></td>
                                        <td class="text-muted align-middle text-truncate" style="max-width: 150px;"
                                            title="<?php echo htmlspecialchars($row['email']); ?>">
                                            <?php echo htmlspecialchars($row['email'] ?: 'N/A'); ?></td>
                                        <td class="text-center align-middle">
                                            <?php if (strtolower($row['estado']) == 'activo'): ?>
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
                                        <td class="align-middle small text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php echo date('d M Y', strtotime($row['fecha_ingreso'])); ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-action btn-action-edit text-primary me-2"
                                                title="Editar"
                                                style="background: rgba(78, 115, 223, 0.1); border-radius: 6px;"
                                                onclick='editMiembro(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-action btn-action-delete text-danger"
                                                title="Eliminar"
                                                style="background: rgba(231, 74, 59, 0.1); border-radius: 6px;"
                                                onclick="deleteMiembro(<?php echo $row['miembro_id']; ?>)">
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

<!-- Modal para agregar/editar miembro -->
<div class="modal fade" id="miembroModal" tabindex="-1" aria-labelledby="miembroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0 rounded-top" style="padding: 1.5rem;">
                <h5 class="modal-title fw-bold" id="miembroModalLabel"><i
                        class="fas fa-user-plus text-primary me-2"></i>Nuevo Miembro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form id="miembroForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" id="miembro_id" name="miembro_id">

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Información Personal</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombres"
                                class="form-label fw-bold text-muted small text-uppercase">Nombres</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="nombres" name="nombres"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="apellidos"
                                class="form-label fw-bold text-muted small text-uppercase">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                        </div>
                        <div class="col-md-4">
                            <label for="no_dpi" class="form-label fw-bold text-muted small text-uppercase">No. DPI /
                                Identificación</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="far fa-id-card text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="no_dpi" name="no_dpi">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_nacimiento"
                                class="form-label fw-bold text-muted small text-uppercase">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        <div class="col-md-4">
                            <label for="sexo" class="form-label fw-bold text-muted small text-uppercase">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo">
                                <option value="">-- Selecciona --</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="estado_civil" class="form-label fw-bold text-muted small text-uppercase">Estado
                                Civil</label>
                            <select class="form-select" id="estado_civil" name="estado_civil">
                                <option value="">-- Selecciona --</option>
                                <option value="Soltero (a)">Soltero (a)</option>
                                <option value="Casado (a)">Casado (a)</option>
                                <option value="Unido (a)">Unido (a)</option>
                                <option value="Divorciado (a)">Divorciado (a)</option>
                                <option value="Viudo (a)">Viudo (a)</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-4 border-bottom pb-2">Información de Contacto</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="direccion"
                                class="form-label fw-bold text-muted small text-uppercase">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-map-marker-alt text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="direccion"
                                    name="direccion" placeholder="Ej. Calle 123, Zona 1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="ciudad"
                                class="form-label fw-bold text-muted small text-uppercase">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tel_celular" class="form-label fw-bold text-muted small text-uppercase">Tel.
                                Celular</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-mobile-alt text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 ps-0" id="tel_celular"
                                    name="tel_celular">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="tel_fijo" class="form-label fw-bold text-muted small text-uppercase">Tel.
                                Fijo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-phone-alt text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 ps-0" id="tel_fijo"
                                    name="tel_fijo">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-bold text-muted small text-uppercase">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email">
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 mt-4 border-bottom pb-2">Ministerio y Opciones</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="familia" class="form-label fw-bold text-muted small text-uppercase">Familia a la
                                que pertenece</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-users text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0" id="familia" name="familia">
                                    <option value="">-- Selecciona (Opcional) --</option>
                                    <?php
                                    // Cargar familias si están disponibles
                                    $famQuery = mysqli_query($conn, "SELECT id, nombre FROM familias");
                                    if ($famQuery) {
                                        while ($fam = mysqli_fetch_assoc($famQuery)) {
                                            echo "<option value='" . $fam['id'] . "'>" . $fam['nombre'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="nivel_estudio" class="form-label fw-bold text-muted small text-uppercase">Nivel
                                de Estudio</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-graduation-cap text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0" id="nivel_estudio" name="nivel_estudio">
                                    <option value="">-- Selecciona --</option>
                                    <option value="Sin Estudios">Sin Estudios</option>
                                    <option value="Primaria">Primaria</option>
                                    <option value="Basicos">Basicos</option>
                                    <option value="Diversificado">Diversificado</option>
                                    <option value="Universitario">Universitario</option>
                                    <option value="Magister">Magister</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="cargo" class="form-label fw-bold text-muted small text-uppercase">Ministerio /
                                Cargo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-briefcase text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0" id="cargo" name="cargo">
                                    <option value="">-- Ninguno --</option>
                                    <option value="Pastor">Pastor</option>
                                    <option value="Lider">Líder</option>
                                    <option value="Músico">Músico</option>
                                    <option value="Ujier">Ujier</option>
                                    <option value="Maestro">Maestro (a)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado de
                                Actividad del Miembro</label>
                            <select class="form-select form-select-lg" id="estado" name="estado"
                                style="font-size: 1rem; border-radius: 8px;" required>
                                <option value="Activo">Activo (Congregándose)</option>
                                <option value="Inactivo">Inactivo (Ausente o Traslado)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light rounded-bottom px-4 pb-4 mt-4 mx-n4 mb-n4">
                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal"
                            style="border-radius: 8px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;"><i
                                class="fas fa-save me-2"></i>Guardar Miembro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addMiembro() {
        document.getElementById('miembroModalLabel').innerHTML = '<i class="fas fa-user-plus text-primary me-2"></i>Nuevo Miembro';
        document.getElementById('miembroForm').reset();
        document.getElementById('miembro_id').value = '';
        var modal = new bootstrap.Modal(document.getElementById('miembroModal'));
        modal.show();
    }

    function editMiembro(miembro) {
        document.getElementById('miembroModalLabel').innerHTML = '<i class="fas fa-user-edit text-primary me-2"></i>Editar Miembro';
        document.getElementById('miembro_id').value = miembro.miembro_id;
        document.getElementById('nombres').value = miembro.nombres;
        document.getElementById('apellidos').value = miembro.apellidos;
        document.getElementById('direccion').value = miembro.direccion;
        document.getElementById('ciudad').value = miembro.ciudad;

        let familiaSelect = document.getElementById('familia');
        for (let i = 0; i < familiaSelect.options.length; i++) {
            if (familiaSelect.options[i].value == miembro.familia) {
                familiaSelect.selectedIndex = i;
                break;
            }
        }

        document.getElementById('tel_celular').value = miembro.tel_celular;
        document.getElementById('tel_fijo').value = miembro.tel_fijo;
        document.getElementById('no_dpi').value = miembro.no_dpi;
        document.getElementById('fecha_nacimiento').value = miembro.fecha_nacimiento;
        document.getElementById('nivel_estudio').value = miembro.nivel_estudio;

        // Match cargo based on value if custom loaded
        let cargoSelect = document.getElementById('cargo');
        for (let i = 0; i < cargoSelect.options.length; i++) {
            if (cargoSelect.options[i].value == miembro.cargo) {
                cargoSelect.selectedIndex = i;
                break;
            }
        }

        document.getElementById('estado_civil').value = miembro.estado_civil;
        document.getElementById('sexo').value = miembro.sexo;
        document.getElementById('email').value = miembro.email;

        let estadoSelect = document.getElementById('estado');
        for (let i = 0; i < estadoSelect.options.length; i++) {
            if (estadoSelect.options[i].value.toLowerCase() === miembro.estado.toLowerCase()) {
                estadoSelect.selectedIndex = i;
                break;
            }
        }

        var modal = new bootstrap.Modal(document.getElementById('miembroModal'));
        modal.show();
    }

    function deleteMiembro(id) {
        if (confirm('¿Estás seguro de que quieres eliminar de forma definitiva este miembro?')) {
            window.location.href = '?delete=' + id;
        }
    }

    $(document).ready(function () {
        // Obtener el campo de búsqueda premium
        const $searchInput = $('input[placeholder="Buscar por Nombre, Apellido, DPI o Ciudad..."]');

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