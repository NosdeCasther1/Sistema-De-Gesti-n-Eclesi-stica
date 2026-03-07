<?php
require_once 'header.php';
?>
<!-- Añadir CSS de Select2 y su tema para Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />




<?php
/**
 * Miembros.php — Vista
 * La lógica de negocio (BD, CRUD, validaciones) vive en components/miembros_logica.php
 */
require_once __DIR__ . '/components/miembros_logica.php';
?>

<!-- Luego, tu archivo JavaScript -->
<script src="/ProyectoIglesia/assets/js/miembros.js"></script>

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
                                            <?php echo htmlspecialchars($row['tel_celular']); ?>
                                        </td>
                                        <td class="text-muted align-middle text-truncate" style="max-width: 120px;"
                                            title="<?php echo htmlspecialchars($row['ciudad']); ?>">
                                            <?php echo htmlspecialchars($row['ciudad']); ?>
                                        </td>
                                        <td class="text-muted align-middle text-truncate" style="max-width: 150px;"
                                            title="<?php echo htmlspecialchars($row['email']); ?>">
                                            <?php echo htmlspecialchars($row['email'] ?: 'N/A'); ?>
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
                                                    <i class="fas fa-ban me-1"></i> Inactivo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle small text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php echo date('d M Y', strtotime($row['fecha_ingreso'])); ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-action text-success me-2"
                                                title="Historial Contribuciones"
                                                style="background: rgba(28, 200, 138, 0.1); border-radius: 6px;"
                                                onclick="verContribuciones(<?php echo $row['miembro_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nombres'] . ' ' . $row['apellidos'])); ?>')">
                                                <i class="fas fa-hand-holding-usd"></i>
                                            </button>
                                            <button class="btn btn-sm btn-action text-info me-2"
                                                title="Historial Asistencias"
                                                style="background: rgba(54, 185, 204, 0.1); border-radius: 6px;"
                                                onclick="verAsistencias(<?php echo $row['miembro_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nombres'] . ' ' . $row['apellidos'])); ?>')">
                                                <i class="far fa-calendar-check"></i>
                                            </button>
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

                    <div class="alert alert-light border shadow-sm mb-4" role="alert" style="border-radius: 8px;">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <span class="small text-muted">Los campos marcados con un asterisco (<span
                                class="text-danger fw-bold">*</span>) son <strong>estrictamente
                                obligatorios</strong>.</span>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Información Personal</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombres" class="form-label fw-bold text-muted small text-uppercase">Nombres
                                <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="nombres" name="nombres"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="apellidos" class="form-label fw-bold text-muted small text-uppercase">Apellidos
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                        </div>
                        <div class="col-md-4">
                            <label for="no_dpi" class="form-label fw-bold text-muted small text-uppercase">No. DPI /
                                Identificación <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="far fa-id-card text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="no_dpi" name="no_dpi"
                                    maxlength="13" pattern="[0-9]{1,13}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_nacimiento"
                                class="form-label fw-bold text-muted small text-uppercase">Fecha de Nacimiento <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="sexo" class="form-label fw-bold text-muted small text-uppercase">Sexo <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">-- Selecciona --</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="estado_civil" class="form-label fw-bold text-muted small text-uppercase">Estado
                                Civil <span class="text-danger">*</span></label>
                            <select class="form-select" id="estado_civil" name="estado_civil" required>
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
                            <label for="direccion" class="form-label fw-bold text-muted small text-uppercase">Dirección
                                <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-map-marker-alt text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="direccion"
                                    name="direccion" placeholder="Ej. Calle 123, Zona 1" required>
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
                                Celular <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-mobile-alt text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 ps-0" id="tel_celular"
                                    name="tel_celular" maxlength="8" pattern="[0-9]{8}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="tel_fijo" class="form-label fw-bold text-muted small text-uppercase">Tel.
                                Fijo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-phone-alt text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 ps-0" id="tel_fijo" name="tel_fijo"
                                    maxlength="8" pattern="[0-9]{8}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                                que pertenece <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-users text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0 select2-familia" id="familia"
                                    name="familia" style="width: 100%;" required>
                                    <option value="">-- Selecciona --</option>
                                    <?php
                                    // Cargar familias si están disponibles
                                    $famQuery = mysqli_query($conn, "SELECT id, nombre, descripcion FROM familias");
                                    if ($famQuery) {
                                        while ($fam = mysqli_fetch_assoc($famQuery)) {
                                            $desc = !empty($fam['descripcion']) ? " - " . htmlspecialchars($fam['descripcion']) : " (ID: " . $fam['id'] . ")";
                                            echo "<option value='" . $fam['id'] . "'>" . htmlspecialchars($fam['nombre']) . $desc . "</option>";
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
                                Actividad del Miembro <span class="text-danger">*</span></label>
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

<!-- Modal Historial Contribuciones -->
<div class="modal fade" id="modalContribuciones" tabindex="-1" aria-hidden="true"
    style="background-color: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0"
            style="border-radius: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden;">
            <div class="modal-header text-white"
                style="background-color: #1e293b; border-bottom: none; padding: 15px 25px;">
                <h5 class="modal-title fw-bold text-white text-uppercase"
                    style="font-size: 1rem; letter-spacing: 0.5px;">
                    <i class="fas fa-hand-holding-usd me-3" style="font-size: 1.2rem;"></i>HISTORIAL DE CONTRIBUCIONES
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"
                    aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #f7f9fc;">
                <div class="row g-4">
                    <!-- Sección Izquierda (Información y Total) -->
                    <div class="col-md-4">
                        <div class="card border-0 mb-4"
                            style="border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                            <div class="card-body text-center py-5">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                    style="width: 100px; height: 100px; background-color: #f1f5f9; color: #1e293b; font-size: 3rem;">
                                    <i class="fas fa-user-alt"></i>
                                </div>
                                <h5 class="fw-bold text-uppercase mb-4" id="nombreContribuciones"
                                    style="color: #333; font-size: 1.1rem; letter-spacing: 0.5px;">...</h5>
                            </div>
                        </div>

                        <div class="card border-0" style="border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                            <div class="card-body p-4 d-flex align-items-center justify-content-center">
                                <div class="text-white rounded d-flex align-items-center justify-content-center me-4 shadow-sm"
                                    style="width: 60px; height: 60px; background-color: #1e293b; border-radius: 8px !important;">
                                    <i class="fas fa-briefcase fa-lg"></i>
                                </div>
                                <div class="text-start">
                                    <h6 class="fw-bold mb-1"
                                        style="font-size: 0.75rem; letter-spacing: 1px; color: #888;">TOTAL OFRENDADO
                                    </h6>
                                    <h2 class="fw-bold mb-0" id="totalContribuciones"
                                        style="color: #1e293b; font-size: 2rem;">Q 0.00</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Derecha (Filtros y Tabla) -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <div class="d-flex align-items-center bg-white border rounded px-3 py-2"
                                style="flex: 1; min-width: 300px; border-radius: 6px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                <i class="far fa-calendar-alt text-muted me-2"></i>
                                <input type="date" id="fechaInicioContrib"
                                    class="form-control border-0 shadow-none p-1 text-muted" style="font-size: 0.9rem;"
                                    title="Fecha inicio">
                                <span class="text-muted mx-2">-</span>
                                <input type="date" id="fechaFinContrib"
                                    class="form-control border-0 shadow-none p-1 text-muted" style="font-size: 0.9rem;"
                                    title="Fecha fin">
                                <button class="btn btn-link p-1 ms-2" onclick="filtrarContribuciones()"
                                    style="color: #1e293b;"><i class="fas fa-search fa-lg"></i></button>
                            </div>
                            <div class="ms-md-3">
                                <button class="btn btn-outline-secondary bg-white fw-bold px-4 py-2"
                                    onclick="imprimirReporteGeneral()"
                                    style="border-radius: 20px; font-size: 0.9rem; color: #555; border-color: #ddd;">
                                    <i class="fas fa-print me-2 text-muted"></i> Imprimir
                                </button>
                            </div>
                        </div>

                        <div class="card border-0" style="border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table align-middle mb-0 border-0">
                                        <thead style="background-color: #fcfcfc;">
                                            <tr>
                                                <th class="ps-4 border-bottom-0 py-3 text-muted"
                                                    style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">
                                                    FECHA</th>
                                                <th class="border-bottom-0 py-3 text-muted"
                                                    style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">
                                                    TIPO</th>
                                                <th class="border-bottom-0 py-3 text-muted"
                                                    style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">
                                                    MONTO</th>
                                                <th class="border-bottom-0 py-3 text-muted"
                                                    style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">
                                                    MODO DE PAGO</th>
                                                <th class="text-center pe-4 border-bottom-0 py-3 text-muted"
                                                    style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">
                                                    ACCIÓN</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyContribuciones" style="border-top: none;">
                                        </tbody>
                                    </table>
                                </div>
                                <div id="loadingContribuciones" class="text-center py-5 d-none">
                                    <div class="spinner-border" style="color: #1e293b;" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3 text-muted" style="font-size: 0.85rem;" id="infoRegistrosContrib">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial Asistencias -->
<div class="modal fade" id="modalAsistencias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0" style="border-radius: 12px;">
            <div class="modal-header bg-info bg-opacity-10 border-bottom-0 rounded-top">
                <h5 class="modal-title fw-bold text-info"><i class="far fa-calendar-check me-2"></i>Asistencias de <span
                        id="nombreAsistencias"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Evento</th>
                                <th>Estado Evento</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyAsistencias">
                        </tbody>
                    </table>
                </div>
                <div id="loadingAsistencias" class="text-center py-4 d-none">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function addMiembro() {
        document.getElementById('miembroModalLabel').innerHTML = '<i class="fas fa-user-plus text-primary me-2"></i>Nuevo Miembro';
        document.getElementById('miembroForm').reset();
        document.getElementById('miembro_id').value = '';
        $('#familia').val(null).trigger('change'); // Limpiar Select2
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

        $('#familia').val(miembro.familia).trigger('change'); // Actualizar valor exacto en Select2

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

    // Inicializar Select2 en la búsqueda de familia
    $(document).ready(function () {
        $('.select2-familia').select2({
            dropdownParent: $('#miembroModal'), // Para que funcione dentro del modal de Bootstrap
            placeholder: "-- Busca una Familia --",
            allowClear: true,
            theme: 'bootstrap-5', // Utilizar tema de Bootstrap si está disponible
            width: '100%'
        });
    });

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

    // Funciones AJAX para Historiales
    // Variable global para almacenar contribuciones y filtrar sin pedir al servidor de nuevo
    window.datosContribuciones = [];

    function verContribuciones(id, nombre) {
        document.getElementById('nombreContribuciones').innerText = nombre;
        document.getElementById('tbodyContribuciones').innerHTML = '';
        document.getElementById('totalContribuciones').innerText = 'Q 0.00';
        document.getElementById('infoRegistrosContrib').innerText = '';
        document.getElementById('fechaInicioContrib').value = '';
        document.getElementById('fechaFinContrib').value = '';
        document.getElementById('loadingContribuciones').classList.remove('d-none');

        var modal = new bootstrap.Modal(document.getElementById('modalContribuciones'));
        modal.show();

        fetch(`obtener_contribuciones.php?id=${id}`)
            .then(res => res.json())
            .then(res => {
                document.getElementById('loadingContribuciones').classList.add('d-none');
                if (res.success) {
                    window.datosContribuciones = res.data;
                    renderizarTablaContribuciones(window.datosContribuciones);
                } else {
                    document.getElementById('tbodyContribuciones').innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error interno del servidor.</td></tr>';
                }
            })
            .catch(err => {
                document.getElementById('loadingContribuciones').classList.add('d-none');
                document.getElementById('tbodyContribuciones').innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar datos.</td></tr>';
            });
    }

    function renderizarTablaContribuciones(datos) {
        let html = '';
        let total = 0;

        if (datos.length > 0) {
            datos.forEach(item => {
                let montoVal = parseFloat(item.monto);
                total += montoVal;
                let montoFormat = montoVal.toFixed(2);
                let badgeColor = item.tipo.toLowerCase() === 'diezmo' ? '#334155' : '#738295'; // Dark blue/slate for Diezmo, Lighter blue/grey for Ofrenda
                let modoPagoClass = (item.modo_pago && item.modo_pago.toUpperCase() === 'EFECTIVO') ? '#10b981' : '#334155';

                html += `<tr style="border-bottom: 1px solid #f0f0f0;">
                    <td class="ps-4" style="color: #555; font-size: 0.9rem;">${item.fecha}</td>
                    <td>
                        <div class="badge text-white px-3 py-1 mb-1" style="background-color: ${badgeColor}; border-radius: 20px; font-weight: 500;">${item.tipo}</div>
                        <br><small class="text-muted" style="font-size:0.8rem;">${item.categoria || item.tipo}</small>
                    </td>
                    <td class="fw-bold" style="color: #333; font-size: 0.95rem;">Q ${montoFormat}</td>
                    <td>
                        <div class="badge text-white px-2 py-1" style="background-color: ${modoPagoClass}; border-radius: 4px; font-weight: 600; font-size: 0.75rem; text-transform: uppercase;">
                            ${item.modo_pago || 'EFECTIVO'}
                        </div>
                    </td>
                    <td class="text-center pe-4">
                        <button class="btn text-white rounded-circle shadow-sm" style="background-color: #3b82f6; width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                            onclick="generarRecibo('${document.getElementById('nombreContribuciones').innerText}', '${item.tipo}', '${montoFormat}', '${item.fecha}', '${item.modo_pago || 'Efectivo'}', '${item.referencia || ''}', '${item.categoria || ''}')" title="Imprimir Recibo">
                            <i class="fas fa-print" style="font-size: 0.9rem;"></i>
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            html = '<tr><td colspan="5" class="text-center text-muted py-5">No se encontraron registros de contribuciones.</td></tr>';
        }

        document.getElementById('tbodyContribuciones').innerHTML = html;
        document.getElementById('totalContribuciones').innerText = `Q ${total.toFixed(2)}`;
        document.getElementById('infoRegistrosContrib').innerText = `Mostrando ${datos.length} registros`;
    }

    function filtrarContribuciones() {
        let inicio = document.getElementById('fechaInicioContrib').value;
        let fin = document.getElementById('fechaFinContrib').value;

        let filtrados = window.datosContribuciones;

        if (inicio || fin) {
            filtrados = window.datosContribuciones.filter(item => {
                let fechaItem = new Date(item.fecha);
                fechaItem.setHours(0, 0, 0, 0);

                let pasa = true;
                if (inicio) {
                    let dIni = new Date(inicio + 'T00:00:00');
                    pasa = pasa && (fechaItem >= dIni);
                }
                if (fin) {
                    let dFin = new Date(fin + 'T23:59:59');
                    pasa = pasa && (fechaItem <= dFin);
                }
                return pasa;
            });
        }

        renderizarTablaContribuciones(filtrados);
    }

    function generarRecibo(nombre, tipo, monto, fecha, metodo, ref, cat) {
        const url = `imprimir_recibo.php?nombre=${encodeURIComponent(nombre)}&tipo=${encodeURIComponent(tipo)}&monto=${encodeURIComponent(monto)}&fecha=${encodeURIComponent(fecha)}&metodo=${encodeURIComponent(metodo)}&ref=${encodeURIComponent(ref)}&cat=${encodeURIComponent(cat)}`;
        window.open(url, '_blank', 'width=800,height=600');
    }

    function imprimirReporteGeneral() {
        let inicio = document.getElementById('fechaInicioContrib').value;
        let fin = document.getElementById('fechaFinContrib').value;
        let nombre = document.getElementById('nombreContribuciones').innerText;

        // Determinar qué datos imprimir (los filtrados actualmente visibles)
        let filtrados = window.datosContribuciones;
        if (inicio || fin) {
            filtrados = window.datosContribuciones.filter(item => {
                let fechaItem = new Date(item.fecha);
                fechaItem.setHours(0, 0, 0, 0);

                let pasa = true;
                if (inicio) pasa = pasa && (fechaItem >= new Date(inicio + 'T00:00:00'));
                if (fin) pasa = pasa && (fechaItem <= new Date(fin + 'T23:59:59'));
                return pasa;
            });
        }

        let total = 0;
        let filasHTML = '';
        filtrados.forEach((item, index) => {
            let montoVal = parseFloat(item.monto);
            total += montoVal;
            filasHTML += `
                <tr>
                    <td>${item.miembro_id || (index + 1)}</td>
                    <td>${item.fecha}</td>
                    <td>${item.tipo}</td>
                    <td>${(item.modo_pago || 'EFECTIVO').toUpperCase()}</td>
                    <td>${item.referencia || ''}</td>
                    <td>Q ${montoVal.toFixed(2)}</td>
                </tr>
            `;
        });

        // Formato para visualización
        function formatoF(f) {
            if (!f) return '';
            let p = f.split('-');
            return p[2] + '/' + p[1] + '/' + p[0];
        }

        let dHoy = new Date();
        let fechaHoyStr = dHoy.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        let textoInicio = inicio ? formatoF(inicio) : 'Histórico Completo';
        let textoFin = fin ? formatoF(fin) : fechaHoyStr;

        let htmlReporte = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Diezmos y Ofrendas</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #111; margin: 0; padding: 25px; }
                .report-top { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; border-bottom: 1px solid #ddd; padding-bottom: 10px;}
                .report-top-sub { font-size: 14px; font-weight: bold; }
                .report-top-date { font-size: 14px; }
                
                .main-header { display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 35px; }
                .logo-container { width: 160px; display: flex; align-items: center; justify-content: center; }
                .logo-container img { max-width: 130px; max-height: 80px; }
                
                .church-info { text-align: center; flex: 1; padding: 0 20px; }
                .church-info h1 { margin: 0; font-size: 22px; color: #1c3d5a; text-transform: uppercase; letter-spacing: 1px; }
                .church-info p { margin: 3px 0; font-size: 14px; color: #555; }
                
                .meta-filter { font-size: 14px; text-align: right; width: 160px; color: #333; display: flex; flex-direction: column; justify-content: center; }
                
                .member-title { text-align: center; font-size: 17px; font-weight: bold; text-transform: uppercase; margin-bottom: 25px; color: #111; }
                
                table { width: 100%; border-collapse: collapse; font-size: 13px; font-weight: bold; margin-bottom: 5px; }
                thead th { background-color: #1e293b; color: #ffffff; padding: 8px 10px; text-align: left; border-right: 1px solid #334155; }
                thead th:last-child { border-right: none; }
                
                tbody td { padding: 8px 10px; border-bottom: 2px solid #5faee3; color: #111; }
                tbody tr:last-child td { border-bottom: 3px solid #1e293b; }

                .total-footer { width: 100%; display: flex; justify-content: flex-end; font-size: 15px; font-weight: bold; margin-top: 5px; color: #111; }
                .total-footer-amount { min-width: 150px; text-align: right; }
            </style>
        </head>
        <body>
            <div class="report-top">
                <div style="width: 160px;"></div>
                <div class="report-top-sub">Reporte de Diezmos</div>
                <div class="report-top-date">${fechaHoyStr}</div>
            </div>
            
            <div class="main-header">
                <div class="logo-container">
                    <img src="/ProyectoIglesia/img/logo.png" alt="Logo">
                </div>
                <div class="church-info">
                    <h1>Asamblea de Dios Rey de Reyes</h1>
                    <p>Zaculeu Central, zona 9, Huehuetenango, Huehuetenango</p>
                    <p>Teléfono: Pendiente</p>
                </div>
                <div class="meta-filter">
                    <div>Inicio: ${textoInicio}</div>
                    <div>Final: ${textoFin}</div>
                </div>
            </div>
            
            <div class="member-title">MIEMBRO: ${nombre}</div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">Fecha</th>
                        <th style="width: 20%;">Tipo</th>
                        <th style="width: 20%;">Modo Pago</th>
                        <th style="width: 20%;">Referencia</th>
                        <th style="width: 20%;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    ${filasHTML}
                </tbody>
            </table>
            
            <div class="total-footer">
                <div class="total-footer-amount" style="border-bottom: double 3px #111;">Q ${total.toFixed(2)}</div>
            </div>
        </body>
        </html>
        `;

        let printFrame = document.getElementById('reportPrintFrame');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'reportPrintFrame';
            printFrame.style.display = 'none';
            document.body.appendChild(printFrame);
        }

        printFrame.contentDocument.open();
        printFrame.contentDocument.write(htmlReporte);
        printFrame.contentDocument.close();

        setTimeout(() => {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        }, 500);
    }

    function verAsistencias(id, nombre) {
        document.getElementById('nombreAsistencias').innerText = nombre;
        document.getElementById('tbodyAsistencias').innerHTML = '';
        document.getElementById('loadingAsistencias').classList.remove('d-none');

        var modal = new bootstrap.Modal(document.getElementById('modalAsistencias'));
        modal.show();

        fetch(`obtener_asistencias.php?id=${id}`)
            .then(res => res.json())
            .then(res => {
                document.getElementById('loadingAsistencias').classList.add('d-none');
                let html = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(item => {
                        html += `<tr>
    <td><i class="far fa-calendar text-muted me-1"></i> ${item.fecha_asistencia}</td>
    <td class="fw-bold">${item.nombre_evento}</td>
    <td><span class="badge bg-secondary">${item.estado}</span></td>
</tr>`;
                    });
                } else {
                    html = `<tr>
    <td colspan="3" class="text-center text-muted py-4">No se registró asistencia a eventos para este miembro.</td>
</tr>`;
                }
                document.getElementById('tbodyAsistencias').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('loadingAsistencias').classList.add('d-none');
                document.getElementById('tbodyAsistencias').innerHTML = `<tr>
    <td colspan="3" class="text-center text-danger">Error al cargar datos.</td>
</tr>`;
            });
    }

</script>

<?php
require_once __DIR__ . '/footer.php';
?>
</body>

</html>