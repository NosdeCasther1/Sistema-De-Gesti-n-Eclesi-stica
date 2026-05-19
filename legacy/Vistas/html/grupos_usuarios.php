<?php
require_once __DIR__ . '/../../Config/conexion.php';
// Verificar sesión y demás de ser necesario, igual que en otras vistas
require_once 'header.php';
?>

<div class="wrapper">
    <?php require_once 'sidebar.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-3 p-md-4 mb-5">

                <!-- Encabezado -->
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 text-dark font-weight-bold mb-1"><i
                                class="fas fa-users-cog text-primary me-2"></i>Grupos de Usuarios</h1>
                        <p class="text-muted mb-0">Gestión de roles y permisos del sistema.</p>
                    </div>
                    <div>
                        <button class="btn btn-primary shadow-sm" onclick="abrirModalNuevo()">
                            <i class="fas fa-plus me-2"></i>Nuevo Grupo
                        </button>
                    </div>
                </div>

                <!-- Tabla Principal -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="tablaGrupos" class="table table-softwys table-hover mb-0 w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th>Nombre del Grupo</th>
                                        <th>Descripción</th>
                                        <th width="10%">Estado</th>
                                        <th width="15%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Datos cargados via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Formulario / Permisos -->
    <div class="modal fade" id="modalGrupo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalTitulo">Nuevo Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <form id="formGrupo">
                        <input type="hidden" id="grupo_id" name="id" value="0">
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

                                <div class="permissions-grid">
                                    <div class="perm-row perm-header">
                                        <div class="perm-col perm-col-modulo">Módulo</div>
                                        <div class="perm-col">Ver</div>
                                        <div class="perm-col">Editar</div>
                                        <div class="perm-col">Eliminar</div>
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
                                        <div class="perm-row">
                                            <div class="perm-col perm-col-modulo">
                                                <?php echo $label; ?>
                                            </div>
                                            <div class="perm-col">
                                                <input class="form-check-input perm-cb" type="checkbox"
                                                    id="v_<?php echo $key; ?>" data-mod="<?php echo $key; ?>"
                                                    data-action="view">
                                            </div>
                                            <div class="perm-col">
                                                <input class="form-check-input perm-cb" type="checkbox"
                                                    id="e_<?php echo $key; ?>" data-mod="<?php echo $key; ?>"
                                                    data-action="edit">
                                            </div>
                                            <div class="perm-col">
                                                <input class="form-check-input perm-cb" type="checkbox"
                                                    id="d_<?php echo $key; ?>" data-mod="<?php echo $key; ?>"
                                                    data-action="delete">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted mt-2 d-block">* Los permisos definen qué opciones podrá ver el
                                    usuario en el menú y las acciones dentro del sistema.</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm"
                        onclick="guardarTodo()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        const urlBase = '/ProyectoIglesia/api/grupos';
        let tablaGrupos;

        $(document).ready(function () {
            initTable();
        });

        function initTable() {
            tablaGrupos = $('#tablaGrupos').DataTable({
                "ajax": {
                    "url": urlBase,
                    "type": "GET",
                    "dataSrc": "data"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "nombre", "render": function (data) { return '<strong>' + data + '</strong>'; } },
                    { "data": "descripcion" },
                    {
                        "data": "estado", "render": function (data) {
                            return '<span class="badge badge-soft badge-activo">Activo</span>';
                        }
                    },
                    {
                        "data": null,
                        "className": "text-center",
                        "render": function (data, type, row) {
                            return `
                                <button class="action-btn" title="Editar Permisos" onclick="editarGrupo(${row.id}, '${row.nombre}', '${row.descripcion || ''}')">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                                <button class="action-btn" title="Eliminar" onclick="eliminarGrupo(${row.id})">
                                    <i class="fas fa-trash-alt text-danger"></i>
                                </button>
                            `;
                        }
                    }
                ],
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
            });
        }

        function abrirModalNuevo() {
            document.getElementById('formGrupo').reset();
            document.getElementById('grupo_id').value = '0';
            document.getElementById('modalTitulo').innerText = 'Nuevo Grupo';

            // Uncheck todos
            document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);

            var modal = new bootstrap.Modal(document.getElementById('modalGrupo'));
            modal.show();
        }

        function editarGrupo(id, nombre, descripcion) {
            document.getElementById('formGrupo').reset();
            document.getElementById('grupo_id').value = id;
            document.getElementById('nombreGrupo').value = nombre;
            document.getElementById('descripcionGrupo').value = descripcion;
            document.getElementById('modalTitulo').innerText = 'Editar Grupo y Permisos';

            // Desmarcar todos antes de cargar
            document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);

            // Cargar Permisos via AJAX
            $.ajax({
                url: urlBase + '/permisos',
                type: 'GET',
                data: { grupo_id: id },
                success: function (response) {
                    if (response.status === 'success' && response.data) {
                        // Recorrer los permisos devueltos y marcar checks
                        Object.keys(response.data).forEach(modulo => {
                            let p = response.data[modulo];
                            if (p.can_view == 1) $(`#v_${modulo}`).prop('checked', true);
                            if (p.can_edit == 1) $(`#e_${modulo}`).prop('checked', true);
                            if (p.can_delete == 1) $(`#d_${modulo}`).prop('checked', true);
                        });
                    }
                    var modal = new bootstrap.Modal(document.getElementById('modalGrupo'));
                    modal.show();
                }
            });
        }

        function recogerPermisos() {
            let permisosObj = {};
            document.querySelectorAll('.perm-cb').forEach(cb => {
                let mod = cb.getAttribute('data-mod');
                let act = cb.getAttribute('data-action'); // view, edit, delete

                if (!permisosObj[mod]) permisosObj[mod] = { view: false, edit: false, delete: false };

                if (cb.checked) {
                    permisosObj[mod][act] = true;
                }
            });
            // Filtrar y quedar solo con modulos que tengan algo activo
            let result = {};
            Object.keys(permisosObj).forEach(mod => {
                if (permisosObj[mod].view || permisosObj[mod].edit || permisosObj[mod].delete) {
                    result[mod] = permisosObj[mod];
                }
            });
            return result;
        }

        function guardarTodo() {
            let id = document.getElementById('grupo_id').value;
            let nombre = document.getElementById('nombreGrupo').value;
            let desc = document.getElementById('descripcionGrupo').value;

            if (!nombre) {
                Swal.fire('Atención', 'El nombre es obligatorio', 'warning');
                return;
            }

            let permisosJSON = JSON.stringify(recogerPermisos());

            // 1. Guardar Grupo
            $.ajax({
                url: urlBase + '/guardar',
                type: 'POST',
                data: {
                    id: id,
                    nombre: nombre,
                    descripcion: desc
                },
                success: function (respGrupo) {
                    let objGrupo = typeof respGrupo == 'string' ? JSON.parse(respGrupo) : respGrupo;
                    if (objGrupo.status === 'success') {

                        let assignedId = id == 0 ? objGrupo.id : id;

                        // 2. Guardar Permisos
                        $.ajax({
                            url: urlBase + '/permisos/guardar',
                            type: 'POST',
                            data: {
                                grupo_id: assignedId,
                                permisos: permisosJSON
                            },
                            success: function (respPerm) {
                                bootstrap.Modal.getInstance(document.getElementById('modalGrupo')).hide();
                                tablaGrupos.ajax.reload(null, false);
                                Swal.fire('Éxito', 'Grupo y permisos guardados correctamente', 'success');
                            }
                        });
                    } else {
                        Swal.fire('Error', objGrupo.message, 'error');
                    }
                }
            });
        }

        function eliminarGrupo(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se eliminarán todos los permisos y puede afectar a los usuarios.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: urlBase + '/eliminar',
                        type: 'POST',
                        data: { id: id },
                        success: function (response) {
                            let obj = typeof response == 'string' ? JSON.parse(response) : response;
                            if (obj.status === 'success') {
                                tablaGrupos.ajax.reload(null, false);
                                Swal.fire('Eliminado!', obj.message, 'success');
                            } else {
                                Swal.fire('Error', obj.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
    <?php require_once 'footer.php'; ?>