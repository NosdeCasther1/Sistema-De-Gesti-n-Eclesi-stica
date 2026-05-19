<!-- Las alertas se manejarán vía SweetAlert2 o dinámicamente -->
<div id="alertContainer"></div>

<!-- Header del Módulo -->
<div class="module-header">
    <div class="module-title-section">
        <ul class="breadcrumb-custom mb-2">
            <li class="breadcrumb-item-custom"><a href="/ProyectoIglesia/inicio">Inicio</a></li>
            <li class="breadcrumb-item-custom active">Gestión de Usuarios</li>
        </ul>
        <h1 class="h2 text-dark font-weight-bold">Usuarios</h1>
        <p class="text-muted mb-0">Administra el acceso, roles e información de los usuarios del sistema.</p>
    </div>
    <div class="module-actions">
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill me-3" style="font-size: 0.9rem;">
            <i class="fas fa-users me-1"></i> <span id="userCount">0</span> Registrados
        </span>
        <button class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2" 
            style="border-radius: 10px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>
</div>

<div class="card-module">
    <div class="card-header-custom">
        <div class="input-group search-group" style="max-width: 500px;">
            <span class="input-group-text bg-transparent border-end-0 text-muted">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 border-end-0 ps-0"
                placeholder="Buscar por nombre, usuario, rol o email...">
            <button class="btn bg-transparent border-start-0 border text-muted clear-search" type="button" id="clearSearch" style="display: none;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom w-100 align-middle" id="usersTable">
            <thead>
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Usuario</th>
                    <th>Credenciales</th>
                    <th class="text-center">Rol</th>
                    <th class="text-center">Grupo</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end">Acceso</th>
                    <th class="text-center pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <!-- Los usuarios se cargarán dinámicamente aquí -->
                <!-- Fila mostrada cuando no hay resultados de búsqueda -->
                <tr id="noResultsRow" style="display: none;">
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted d-flex flex-column align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-search fa-2x text-secondary opacity-50"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No se encontraron usuarios</h5>
                            <p class="mb-0">No hay registros que coincidan con tu búsqueda.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Usuario Rediseñado Premium -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="modal-title fw-bold text-dark fs-4" id="modalNuevoUsuarioLabel">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle me-2 shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="formNuevoUsuario">
                <div class="modal-body px-4 pb-4 px-md-5">
                    <p class="text-muted mb-4 pb-2 border-bottom">Completa la información para autorizar un nuevo acceso
                        al CRM de la iglesia.</p>


                    <div class="mb-4">
                        <label for="nombres"
                            class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Nombres
                            Completos</label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i
                                    class="far fa-address-card"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="nombres" name="nombres"
                                placeholder="Ej. Juan Pérez" required style="font-size: 1rem;">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="usuario"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Usuario
                                (Login)</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="far fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="usuario" name="usuario"
                                    placeholder="Ej. jperez" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="rol"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Rol en
                                Sistema</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="fas fa-users-cog"></i></span>
                                <select class="form-select border-start-0 ps-0" id="rol" name="rol" required
                                    style="font-size: 1rem;">
                                    <option value="administrador">Administrador</option>
                                    <option value="pastor">Pastor</option>
                                    <option value="secretario">Secretario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="grupo_id"
                            class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">
                            <i class="fas fa-layer-group text-primary me-1"></i> Grupo de Usuario
                        </label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i
                                    class="fas fa-user-shield"></i></span>
                             <select class="form-select border-start-0 ps-0" id="grupo_id" name="grupo_id"
                                style="font-size: 1rem;">
                                <option value="">— Cargando grupos... —</option>
                            </select>
                        </div>
                        <small class="text-muted">Opcional. Define los permisos que tendrá este usuario en el
                            sistema.</small>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="email"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Correo
                                Electrónico</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="far fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email"
                                    placeholder="usuario@correo.com" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Contraseña</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i
                                        class="fas fa-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password"
                                    name="password" placeholder="Mínimo 8 caracteres" required style="font-size: 1rem;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-4 d-flex justify-content-end"
                    style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-white text-muted border px-4 py-2 me-2 shadow-sm"
                        data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-save me-2"></i>Registrar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="modal-title fw-bold text-dark fs-4" id="modalEditarUsuarioLabel">
                    <div class="bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center rounded-circle me-2 shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    Editar Perfil de Usuario
                </h5>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="formEditarUsuario">
                <div class="modal-body px-4 pb-4 px-md-5">
                    <p class="text-muted mb-4 pb-2 border-bottom">Actualiza la información del acceso al sistema de este
                        usuario.</p>

                    <input type="hidden" name="id_usuario" id="edit_id_usuario" value="">

                    <div class="mb-4">
                        <label for="edit_nombres"
                            class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Nombres
                            Completos</label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                            <span class="input-group-text bg-white border-end-0 text-info"><i
                                    class="far fa-address-card"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="edit_nombres"
                                name="edit_nombres" required style="font-size: 1rem;">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="edit_usuario"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Usuario
                                (Login)</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i
                                        class="far fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="edit_usuario"
                                    name="edit_usuario" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_rol"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Rol en
                                Sistema</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i
                                        class="fas fa-users-cog"></i></span>
                                <select class="form-select border-start-0 ps-0" id="edit_rol" name="edit_rol" required
                                    style="font-size: 1rem;">
                                    <option value="administrador">Administrador</option>
                                    <option value="pastor">Pastor</option>
                                    <option value="secretario">Secretario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="edit_grupo_id"
                            class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">
                            <i class="fas fa-layer-group text-info me-1"></i> Grupo de Usuario
                        </label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                            <span class="input-group-text bg-white border-end-0 text-info"><i
                                    class="fas fa-user-shield"></i></span>
                            <select class="form-select border-start-0 ps-0" id="edit_grupo_id" name="edit_grupo_id"
                                style="font-size: 1rem;">
                                <option value="">— Cargando grupos... —</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="edit_email"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Correo
                                Electrónico</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i
                                        class="far fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-0" id="edit_email"
                                    name="edit_email" required style="font-size: 1rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password"
                                class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Cambiar
                                Contraseña</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text bg-white border-end-0 text-info"><i
                                        class="fas fa-key"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0" id="edit_password"
                                    name="edit_password" placeholder="(Opcional) Déjalo en blanco si no la cambias"
                                    style="font-size: 0.9rem;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-4 d-flex justify-content-end"
                    style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-white text-muted border px-4 py-2 me-2 shadow-sm"
                        data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white px-4 py-2 shadow-sm"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-elevate:hover {
        transform: translateY(-2px);
        box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .15);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalEdit = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
        const modalNew = new bootstrap.Modal(document.getElementById('modalNuevoUsuario'));
        const tableBody = document.getElementById('usersTableBody');
        const userCount = document.getElementById('userCount');
        const searchInput = document.getElementById('searchInput');

        let allUsers = [];

        // 1. Cargar Grupos para los selects
        fetch('/ProyectoIglesia/api/grupos')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const options = '<option value="">— Sin grupo asignado —</option>' + 
                        res.data.map(g => `<option value="${g.id}">${g.nombre}</option>`).join('');
                    document.getElementById('grupo_id').innerHTML = options;
                    document.getElementById('edit_grupo_id').innerHTML = options;
                }
            });

        // 2. Cargar Usuarios
        function loadUsers() {
            fetch('/ProyectoIglesia/_/u')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        allUsers = res.data;
                        renderUsers(allUsers);
                    }
                });
        }

        function renderUsers(users) {
            tableBody.innerHTML = '';
            userCount.textContent = users.length;
            
            if (users.length === 0) {
                document.getElementById('noResultsRow').style.display = 'table-row';
                return;
            }
            document.getElementById('noResultsRow').style.display = 'none';

            users.forEach(user => {
                const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14'];
                const initials = user.nombres.substring(0, 1).toUpperCase();
                const avatarColor = colors[user.id_usuario % colors.length];
                const lastLogin = user.ultimo_login ? 
                    `<div class="small fw-bold text-dark mb-1">${user.ultimo_login.split(' ')[0]}</div>
                     <div class="small text-muted"><i class="far fa-clock me-1"></i>${user.ultimo_login.split(' ')[1]}</div>` :
                    `<span class="badge bg-light text-muted border px-2 py-1"><i class="fas fa-minus"></i> Nunca</span>`;

                const row = document.createElement('tr');
                row.className = 'user-row';
                row.innerHTML = `
                    <td class="text-muted fw-bold small ps-3">#${user.id_usuario.toString().padStart(3, '0')}</td>
                    <td>
                        <div class="d-flex align-items-center py-2">
                            <div class="avatar text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                                style="background-color: ${avatarColor}; width: 42px; height: 42px; font-weight: 600; font-size: 1.1rem; border: 2px solid white;">
                                ${initials}
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6">${user.nombres}</div>
                                <div class="small text-muted d-flex align-items-center mt-1">
                                    <i class="far fa-calendar-plus me-1 opacity-75"></i> Reg: ${user.created_at.split(' ')[0]}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-secondary mb-1"><i class="fas fa-user-circle me-1 text-primary opacity-50"></i>${user.username}</div>
                        <div class="small text-muted"><i class="fas fa-envelope me-1 text-info opacity-50"></i>${user.email}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm">
                            <i class="fas fa-shield-alt me-1"></i> ${user.role}
                        </span>
                    </td>
                    <td class="text-center">
                        ${user.nombre_grupo ? `<span class="badge" style="background:#eff6ff;color:#1e40af;border-radius:6px;padding:.4em .8em;font-size:0.8rem;"><i class="fas fa-layer-group me-1"></i>${user.nombre_grupo}</span>` : '<span class="text-muted small">—</span>'}
                    </td>
                    <td class="text-center">
                        <span class="badge ${user.status === 'activo' ? 'bg-success' : 'bg-secondary'} bg-opacity-10 ${user.status === 'activo' ? 'text-success' : 'text-secondary'} rounded-pill px-3 py-2 fw-semibold shadow-sm">
                            <i class="fas ${user.status === 'activo' ? 'fa-circle' : 'fa-ban'} me-1" style="font-size: 0.6rem;"></i> ${user.status}
                        </span>
                    </td>
                    <td class="text-end">${lastLogin}</td>
                    <td class="text-center pe-3">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-sm text-primary hover-elevate btn-edit" title="Editar" 
                                style="background: rgba(78, 115, 223, 0.1); border-radius: 8px; border: 1px solid rgba(78, 115, 223, 0.2); width: 34px; height: 34px;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm ${user.status === 'activo' ? 'text-danger' : 'text-success'} hover-elevate btn-toggle" title="${user.status === 'activo' ? 'Desactivar' : 'Activar'}"
                                style="background: rgba(${user.status === 'activo' ? '231, 74, 59' : '28, 200, 138'}, 0.1); border-radius: 8px; border: 1px solid rgba(${user.status === 'activo' ? '231, 74, 59' : '28, 200, 138'}, 0.2); width: 34px; height: 34px;">
                                <i class="fas ${user.status === 'activo' ? 'fa-power-off' : 'fa-check'}"></i>
                            </button>
                            <button class="btn btn-sm text-danger hover-elevate btn-delete" title="Eliminar"
                                style="background: rgba(231, 74, 59, 0.1); border-radius: 8px; border: 1px solid rgba(231, 74, 59, 0.2); width: 34px; height: 34px;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                `;

                // Event Listeners for actions
                row.querySelector('.btn-edit').onclick = () => openEditModal(user);
                row.querySelector('.btn-toggle').onclick = () => toggleStatus(user);
                row.querySelector('.btn-delete').onclick = () => deleteUser(user);

                tableBody.appendChild(row);
            });
        }

        // 3. Acciones
        function openEditModal(user) {
            document.getElementById('edit_id_usuario').value = user.id_usuario;
            document.getElementById('edit_nombres').value = user.nombres;
            document.getElementById('edit_usuario').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_rol').value = user.role;
            document.getElementById('edit_grupo_id').value = user.grupo_id || '';
            document.getElementById('edit_password').value = '';
            modalEdit.show();
        }

        document.getElementById('formNuevoUsuario').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/ProyectoIglesia/_/u/save', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        modalNew.hide();
                        loadUsers();
                        Swal.fire('¡Éxito!', res.message, 'success');
                        this.reset();
                    } else Swal.fire('Error', res.message, 'error');
                });
        };

        document.getElementById('formEditarUsuario').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/ProyectoIglesia/api/usuarios/guardar', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        modalEdit.hide();
                        loadUsers();
                        Swal.fire('¡Actualizado!', res.message, 'success');
                    } else Swal.fire('Error', res.message, 'error');
                });
        };

        function toggleStatus(user) {
            Swal.fire({
                title: user.status === 'activo' ? '¿Desactivar?' : '¿Activar?',
                text: 'El estado del usuario será cambiado.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                confirmButtonColor: user.status === 'activo' ? '#e74a3b' : '#1cc88a'
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id_usuario', user.id_usuario);
                    formData.append('current_status', user.status);
                    fetch('/ProyectoIglesia/_/u/status', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') { loadUsers(); Swal.fire('¡Hecho!', res.message, 'success'); }
                            else Swal.fire('Error', res.message, 'error');
                        });
                }
            });
        }

        function deleteUser(user) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                confirmButtonColor: '#e74a3b'
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id_usuario', user.id_usuario);
                    fetch('/ProyectoIglesia/_/u/del', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') { loadUsers(); Swal.fire('¡Eliminado!', res.message, 'success'); }
                            else Swal.fire('Error', res.message, 'error');
                        });
                }
            });
        }

        // 4. Búsqueda con botón de limpiar
        searchInput.addEventListener('input', function() {
            const clearBtn = document.getElementById('clearSearch');
            if (this.value.length > 0) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }

            const term = this.value.toLowerCase();
            const filtered = allUsers.filter(u => 
                u.nombres.toLowerCase().includes(term) || 
                u.username.toLowerCase().includes(term) || 
                u.email.toLowerCase().includes(term) ||
                u.role.toLowerCase().includes(term)
            );
            renderUsers(filtered);
        });

        document.getElementById('clearSearch').addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            renderUsers(allUsers);
            searchInput.focus();
        });

        // Inicializar
        loadUsers();
    });
</script>