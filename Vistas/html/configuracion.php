<?php
include 'header.php';
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener datos actuales de configuración
$sql = "SELECT * FROM configuracion_sistema LIMIT 1";
$res = mysqli_query($conn, $sql);
$configuracion = mysqli_fetch_assoc($res);

if (!$configuracion) {
    // Si por alguna razón no existe, usamos placeholders
    $configuracion = [
        'nombre_iglesia' => '',
        'telefono' => '',
        'correo' => '',
        'direccion' => '',
        'moneda' => 'Q',
        'logo_url' => 'default_logo.png'
    ];
}

?>
<?php
// Determinar pestaña activa desde URL (ej: ?tab=usuarios)
$active_tab = in_array($_GET['tab'] ?? '', ['general', 'usuarios', 'grupos', 'backup'])
    ? $_GET['tab']
    : 'general';
?>
<style>
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background-color: #4e73df;
        color: white;
    }

    .nav-pills .nav-link {
        color: #5a5c69;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .nav-pills .nav-link:hover {
        background-color: #eaecf4;
    }

    .tab-pane {
        padding: 20px 0;
    }

    .logo-preview {
        width: 150px;
        height: 150px;
        object-fit: contain;
        border: 2px dashed #d1d3e2;
        border-radius: 10px;
        padding: 10px;
    }
</style>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container-fluid py-4 px-4">
            <div class="page-header mb-4">
                <h1 class="h2 text-dark font-weight-bold">Ajustes del Sistema</h1>
                <p class="text-muted">Gestiona la información de la iglesia, usuarios y copias de seguridad desde un
                    solo lugar.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Pestañas Laterales -->
                        <div class="col-md-3 border-end">
                            <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <button
                                    class="nav-link text-start <?php echo $active_tab === 'general' ? 'active' : ''; ?>"
                                    id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general"
                                    type="button" role="tab" aria-controls="v-pills-general"
                                    aria-selected="<?php echo $active_tab === 'general' ? 'true' : 'false'; ?>">
                                    <i class="fas fa-church me-2 w-20px text-center"></i> Información de la Iglesia
                                </button>
                                <button
                                    class="nav-link text-start <?php echo $active_tab === 'usuarios' ? 'active' : ''; ?>"
                                    id="v-pills-usuarios-tab" data-bs-toggle="pill" data-bs-target="#v-pills-usuarios"
                                    type="button" role="tab" aria-controls="v-pills-usuarios"
                                    aria-selected="<?php echo $active_tab === 'usuarios' ? 'true' : 'false'; ?>">
                                    <i class="fas fa-users-cog me-2 w-20px text-center"></i> Gestión de Usuarios
                                </button>
                                <button
                                    class="nav-link text-start <?php echo $active_tab === 'grupos' ? 'active' : ''; ?>"
                                    id="v-pills-grupos-tab" data-bs-toggle="pill" data-bs-target="#v-pills-grupos"
                                    type="button" role="tab" aria-controls="v-pills-grupos"
                                    aria-selected="<?php echo $active_tab === 'grupos' ? 'true' : 'false'; ?>">
                                    <i class="fas fa-user-shield me-2 w-20px text-center"></i> Grupos de Usuarios
                                </button>
                                <button
                                    class="nav-link text-start <?php echo $active_tab === 'backup' ? 'active' : ''; ?>"
                                    id="v-pills-backup-tab" data-bs-toggle="pill" data-bs-target="#v-pills-backup"
                                    type="button" role="tab" aria-controls="v-pills-backup"
                                    aria-selected="<?php echo $active_tab === 'backup' ? 'true' : 'false'; ?>">
                                    <i class="fas fa-database me-2 w-20px text-center"></i> Copias de Seguridad
                                </button>
                            </div>
                        </div>

                        <!-- Contenido de las Pestañas -->
                        <div class="col-md-9 pt-3 pt-md-0 ps-md-4">
                            <div class="tab-content" id="v-pills-tabContent">

                                <!-- Tab: Información General -->
                                <div class="tab-pane fade <?php echo $active_tab === 'general' ? 'show active' : ''; ?>"
                                    id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab">
                                    <h4 class="mb-4 text-primary font-weight-bold">Datos Generales</h4>

                                    <form id="formConfiguracion" enctype="multipart/form-data">
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-auto">
                                                <img src="/ProyectoIglesia/assets/img/<?php echo htmlspecialchars($configuracion['logo_url']); ?>"
                                                    alt="Logo Iglesia" class="logo-preview mb-2" id="previewLogo"
                                                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZWVlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzU1NSIgZWRtcz0iY2VudGVyIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+U2luIExvZ288L3RleHQ+PC9zdmc+'">
                                            </div>
                                            <div class="col">
                                                <label class="form-label fw-bold">Logotipo Oficial</label>
                                                <input type="file" class="form-control" name="logo" id="logo_input"
                                                    accept="image/*" onchange="previewImage(this)">
                                                <small class="text-muted">Recomendado: Imagen cuadrada en formato PNG
                                                    transparente.</small>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label text-muted small fw-bold">Nombre de la
                                                    Iglesia/Ministerio</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="fas fa-synagogue text-muted"></i></span>
                                                    <input type="text" class="form-control" name="nombre_iglesia"
                                                        value="<?php echo htmlspecialchars($configuracion['nombre_iglesia']); ?>"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-muted small fw-bold">Símbolo
                                                    Moneda</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="fas fa-money-bill-wave text-muted"></i></span>
                                                    <input type="text" class="form-control" name="moneda"
                                                        value="<?php echo htmlspecialchars($configuracion['moneda']); ?>"
                                                        maxlength="5" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted small fw-bold">Teléfono de
                                                    Contacto</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="fas fa-phone text-muted"></i></span>
                                                    <input type="text" class="form-control" name="telefono"
                                                        value="<?php echo htmlspecialchars($configuracion['telefono']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small fw-bold">Correo
                                                    Electrónico</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="fas fa-envelope text-muted"></i></span>
                                                    <input type="email" class="form-control" name="correo"
                                                        value="<?php echo htmlspecialchars($configuracion['correo']); ?>">
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label text-muted small fw-bold">Dirección
                                                    Física</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="fas fa-map-marker-alt text-muted"></i></span>
                                                    <input type="text" class="form-control" name="direccion"
                                                        value="<?php echo htmlspecialchars($configuracion['direccion']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 text-end">
                                            <button type="submit" class="btn btn-primary px-4 shadow-sm"
                                                style="border-radius: 8px;">
                                                <i class="fas fa-save me-2"></i>Guardar Cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Tab: Usuarios -->
                                <div class="tab-pane fade <?php echo $active_tab === 'usuarios' ? 'show active' : ''; ?>"
                                    id="v-pills-usuarios" role="tabpanel" aria-labelledby="v-pills-usuarios-tab">
                                    <div
                                        style="width: 100%; display:block; padding: 0; margin: 0; overflow: hidden; border: none;">
                                        <?php
                                        $isComponent = true;
                                        include __DIR__ . '/components/usuarios_logica.php';
                                        include __DIR__ . '/components/usuarios_ui.php';
                                        ?>
                                    </div>
                                </div>

                                <!-- Tab: Grupos de Usuarios -->
                                <div class="tab-pane fade <?php echo $active_tab === 'grupos' ? 'show active' : ''; ?>"
                                    id="v-pills-grupos" role="tabpanel" aria-labelledby="v-pills-grupos-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <h4 class="mb-1 text-primary font-weight-bold">Grupos de Usuarios</h4>
                                            <p class="text-muted small mb-0">Define roles con permisos específicos por
                                                módulo.</p>
                                        </div>
                                        <button class="btn btn-primary shadow-sm" onclick="abrirModalNuevo()">
                                            <i class="fas fa-plus me-2"></i>Nuevo Grupo
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tablaGrupos" class="table table-hover align-middle w-100"
                                            style="font-size:0.92rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Nombre del Grupo</th>
                                                    <th>Descripción</th>
                                                    <th width="10%" class="text-center">Estado</th>
                                                    <th width="12%" class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab: Backup y Restore -->
                                <div class="tab-pane fade <?php echo $active_tab === 'backup' ? 'show active' : ''; ?>"
                                    id="v-pills-backup" role="tabpanel" aria-labelledby="v-pills-backup-tab">
                                    <h4 class="mb-3 text-primary font-weight-bold">Centro de Respaldo de Datos</h4>
                                    <p class="text-muted mb-4">Es vital mantener copias de seguridad regulares para
                                        prevenir pérdida de información.</p>

                                    <div class="row g-4">
                                        <!-- Tarjeta Backup -->
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 bg-light shadow-sm"
                                                style="border-radius: 12px;">
                                                <div class="card-body text-center p-4">
                                                    <div class="mb-3">
                                                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                                            style="width: 80px; height: 80px;">
                                                            <i class="fas fa-cloud-download-alt fa-2x text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="fw-bold text-dark">Exportar Base de Datos</h5>
                                                    <p class="text-muted small mb-4">Descarga un archivo .sql con toda
                                                        la información actual de miembros, flujos financieros, eventos y
                                                        configuraciones.</p>

                                                    <button class="btn btn-primary w-100 shadow-sm"
                                                        onclick="descargarBackup()" style="border-radius: 8px;">
                                                        <i class="fas fa-download me-2"></i>Generar Copia (.sql)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tarjeta Restore -->
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm"
                                                style="background-color: #fffaf9; border: 1px dashed #e74a3b !important; border-radius: 12px;">
                                                <div class="card-body text-center p-4">
                                                    <div class="mb-3">
                                                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                                            style="width: 80px; height: 80px;">
                                                            <i class="fas fa-trash-Restore-alt fa-2x text-danger"
                                                                style="transform: scaleX(-1);"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="fw-bold text-danger">Restaurar Sistema</h5>
                                                    <p class="text-muted small mb-4">Sube un archivo .sql previamente
                                                        generado. <strong class="text-danger">Advertencia:</strong> Esto
                                                        sobreescribirá todos los datos actuales.</p>

                                                    <form id="formRestore" enctype="multipart/form-data">
                                                        <div class="input-group mb-3">
                                                            <input type="file" class="form-control" name="backup_file"
                                                                id="backup_file" accept=".sql" required
                                                                style="border-radius: 6px 0 0 6px;">
                                                            <button class="btn btn-outline-danger" type="submit"
                                                                id="btnRestore" style="border-radius: 0 6px 6px 0;">
                                                                <i class="fas fa-upload me-1"></i>Restaurar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Grupos de Usuarios -->
<div class="modal fade" id="modalGrupo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTitulo">Nuevo Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <form id="formGrupo">
                    <input type="hidden" id="grupo_id" value="0">
                    <div class="row">
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-3 text-secondary">Datos del Grupo</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Nombre</label>
                                <input type="text" class="form-control" id="nombreGrupo" required
                                    placeholder="Ej. Administradores">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Descripción</label>
                                <textarea class="form-control" id="descripcionGrupo" rows="4"
                                    placeholder="Breve detalle..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3 text-secondary">Permisos de Módulos</h6>
                            <div style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                                <div class="d-flex fw-bold bg-light"
                                    style="font-size:0.82rem; text-transform:uppercase; letter-spacing:0.5px;">
                                    <div style="flex:2; padding:0.6rem 1rem;">Módulo</div>
                                    <div style="flex:1; text-align:center; padding:0.6rem;">Ver</div>
                                    <div style="flex:1; text-align:center; padding:0.6rem;">Editar</div>
                                    <div style="flex:1; text-align:center; padding:0.6rem;">Eliminar</div>
                                </div>
                                <?php
                                $modulos = [
                                    'inicio' => 'Inicio',
                                    'miembros' => 'Miembros / Familias',
                                    'celulas' => 'Células Familiares',
                                    'eventos' => 'Eventos y Asistencia',
                                    'tesoreria' => 'Tesorería',
                                    'bienes' => 'Bienes y Muebles',
                                    'reportes' => 'Reportes',
                                    'configuracion' => 'Configuración'
                                ];
                                foreach ($modulos as $key => $label):
                                    ?>
                                    <div class="d-flex align-items-center" style="border-top:1px solid #e2e8f0;">
                                        <div
                                            style="flex:2; padding:0.6rem 1rem; color:#4a5568; font-weight:500; font-size:0.88rem;">
                                            <?php echo $label; ?>
                                        </div>
                                        <div style="flex:1; text-align:center;">
                                            <input class="form-check-input perm-cb" type="checkbox"
                                                id="v_<?php echo $key; ?>" data-mod="<?php echo $key; ?>" data-action="view"
                                                style="width:1.15em;height:1.15em;cursor:pointer;">
                                        </div>
                                        <div style="flex:1; text-align:center;">
                                            <input class="form-check-input perm-cb" type="checkbox"
                                                id="e_<?php echo $key; ?>" data-mod="<?php echo $key; ?>" data-action="edit"
                                                style="width:1.15em;height:1.15em;cursor:pointer;">
                                        </div>
                                        <div style="flex:1; text-align:center;">
                                            <input class="form-check-input perm-cb" type="checkbox"
                                                id="d_<?php echo $key; ?>" data-mod="<?php echo $key; ?>"
                                                data-action="delete" style="width:1.15em;height:1.15em;cursor:pointer;">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted mt-2 d-block">Los permisos definen qué módulos puede ver y operar
                                este grupo.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm"
                    onclick="guardarGrupo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('previewLogo').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // AJAX para guardar configuración general
    document.getElementById('formConfiguracion').addEventListener('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        Swal.fire({
            title: 'Guardando...',
            text: 'Actualizando información de la iglesia',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('guardar_configuracion.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refrescar para ver el logo nuevo en caso de cambio
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: 'Ha ocurrido un error al procesar la solicitud.'
                });
            });
    });

    // Función descargar backup
    function descargarBackup() {
        Swal.fire({
            title: '¿Generar copia de seguridad?',
            text: "Se descargará un archivo .sql con todos los datos.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, descargar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'generar_backup.php';
            }
        });
    }

    // AJAX para restore
    document.getElementById('formRestore').addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('backup_file');
        if (!fileInput.files.length) {
            return Swal.fire('Error', 'Selecciona un archivo .sql', 'error');
        }

        Swal.fire({
            title: '¿ESTÁS SEGURO?',
            html: '<p class="text-danger fw-bold">Esta acción ELIMINARÁ la base de datos actual y la reemplazará por la del archivo seleccionado.</p><p>Asegúrate de saber lo que haces.</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, ¡Restaurar Sistema!',
            cancelButtonText: 'Cancelar',
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {

                let formData = new FormData(this);

                Swal.fire({
                    title: 'Restaurando Base de Datos...',
                    text: 'Esto puede tardar varios segundos. No cierres la ventana.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('procesar_restore.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Restauración Exitosa',
                                text: 'El sistema ha sido restaurado al punto de backup seleccionado.',
                                confirmButtonColor: '#1cc88a'
                            }).then(() => {
                                window.location.href = 'Login.php'; // Forzar login tras restore
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error de Restauración',
                                text: data.message || 'Ha ocurrido un error.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Crítico',
                            text: 'Fallo de comunicación con el servidor durante el restore.'
                        });
                    });
            }
        });
    });
    // AJAX para restore (Código existente)
    // ... omitiremos reemplazarlo para no desbordar, sólo agregamos abajo

    // Persistencia de Pestaña activa (Tab) en recargas
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tabName = urlParams.get('tab');
        if (tabName) {
            const targetTab = document.querySelector('#v-pills-' + tabName + '-tab');
            if (targetTab) {
                // Usar click() es más confiable que new bootstrap.Tab() ya que
                // data-bs-toggle="pill" ya está conectado por Bootstrap al cargar.
                targetTab.click();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // Inicializar DataTable de Grupos cuando se active la pestaña
        const gruposTab = document.getElementById('v-pills-grupos-tab');
        if (gruposTab) {
            gruposTab.addEventListener('shown.bs.tab', function () {
                if (typeof $.fn !== 'undefined' && !$.fn.DataTable.isDataTable('#tablaGrupos')) {
                    initGruposTable();
                }
            });
        }
    });

    // =============================================
    // MÓDULO GRUPOS DE USUARIOS
    // =============================================
    const urlGrupos = '/ProyectoIglesia/Controladores/GruposUsuariosController.php';
    let tablaGrupos;

    function initGruposTable() {
        tablaGrupos = $('#tablaGrupos').DataTable({
            ajax: { url: urlGrupos + '?action=get_grupos', dataSrc: 'data' },
            columns: [
                { data: 'id' },
                { data: 'nombre', render: d => '<strong>' + d + '</strong>' },
                { data: 'descripcion', defaultContent: '<span class="text-muted">—</span>' },
                { data: 'estado', render: () => '<span class="badge" style="background:#e6fffa;color:#234e52;border-radius:6px;padding:.4em .8em;">Activo</span>' },
                {
                    data: null, className: 'text-center', render: (d, t, row) => `
                    <button class="btn btn-sm btn-light me-1" title="Editar" onclick="editarGrupo(${row.id},'${row.nombre}','${(row.descripcion || '').replace(/'/g, "\\'")}')"><i class="fas fa-edit text-primary"></i></button>
                    <button class="btn btn-sm btn-light" title="Eliminar" onclick="eliminarGrupo(${row.id})"><i class="fas fa-trash-alt text-danger"></i></button>
                ` }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
        });
    }

    function abrirModalNuevo() {
        document.getElementById('grupo_id').value = '0';
        document.getElementById('nombreGrupo').value = '';
        document.getElementById('descripcionGrupo').value = '';
        document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);
        document.getElementById('modalTitulo').innerText = 'Nuevo Grupo';
        new bootstrap.Modal(document.getElementById('modalGrupo')).show();
    }

    function editarGrupo(id, nombre, descripcion) {
        document.getElementById('grupo_id').value = id;
        document.getElementById('nombreGrupo').value = nombre;
        document.getElementById('descripcionGrupo').value = descripcion;
        document.getElementById('modalTitulo').innerText = 'Editar Grupo y Permisos';
        document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);

        $.get(urlGrupos, { action: 'get_permisos', grupo_id: id }, function (resp) {
            if (resp.status === 'success') {
                Object.keys(resp.data).forEach(mod => {
                    let p = resp.data[mod];
                    if (p.can_view == 1) $(`#v_${mod}`).prop('checked', true);
                    if (p.can_edit == 1) $(`#e_${mod}`).prop('checked', true);
                    if (p.can_delete == 1) $(`#d_${mod}`).prop('checked', true);
                });
            }
        });
        new bootstrap.Modal(document.getElementById('modalGrupo')).show();
    }

    function recogerPermisos() {
        let result = {};
        document.querySelectorAll('.perm-cb').forEach(cb => {
            let mod = cb.getAttribute('data-mod');
            let act = cb.getAttribute('data-action');
            if (!result[mod]) result[mod] = { view: false, edit: false, delete: false };
            if (cb.checked) result[mod][act] = true;
        });
        return result;
    }

    function guardarGrupo() {
        let id = document.getElementById('grupo_id').value;
        let nombre = document.getElementById('nombreGrupo').value.trim();
        let desc = document.getElementById('descripcionGrupo').value.trim();
        if (!nombre) { Swal.fire('Atención', 'El nombre del grupo es obligatorio.', 'warning'); return; }

        $.post(urlGrupos, { action: 'save_grupo', id, nombre, descripcion: desc }, function (resp) {
            let assignedId = id == 0 ? resp.id : id;
            if (resp.status === 'success') {
                $.post(urlGrupos, { action: 'save_permisos', grupo_id: assignedId, permisos: JSON.stringify(recogerPermisos()) }, function () {
                    bootstrap.Modal.getInstance(document.getElementById('modalGrupo')).hide();
                    tablaGrupos.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: 'Grupo y permisos actualizados.', timer: 2000, showConfirmButton: false });
                });
            } else {
                Swal.fire('Error', resp.message, 'error');
            }
        });
    }

    function eliminarGrupo(id) {
        Swal.fire({ title: '¿Eliminar este grupo?', text: 'Los permisos asociados también se borrarán.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, eliminar' })
            .then(result => {
                if (result.isConfirmed) {
                    $.post(urlGrupos, { action: 'delete_grupo', id }, function (resp) {
                        if (resp.status === 'success') { tablaGrupos.ajax.reload(null, false); Swal.fire('Eliminado', resp.message, 'success'); }
                        else Swal.fire('Error', resp.message, 'error');
                    });
                }
            });
    }

</script>

</body>

</html>

<?php include 'footer.php'; ?>