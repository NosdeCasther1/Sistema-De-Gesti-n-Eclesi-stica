<style>
    /* Corrección de posicionamiento para Select2 en Modales */
    .select2-container {
        z-index: 2000 !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        z-index: 2001 !important;
    }
</style>
<!-- Header del Módulo -->
<div class="module-header">
    <div class="module-title-section">
        <ul class="breadcrumb-custom mb-2">
            <li class="breadcrumb-item-custom"><a href="/ProyectoIglesia/inicio">Inicio</a></li>
            <li class="breadcrumb-item-custom active">Gestión de Miembros</li>
        </ul>
        <h1 class="h2 text-dark font-weight-bold">Miembros</h1>
        <p class="text-muted mb-0">Administra los registros y datos de la congregación.</p>
    </div>
    <div class="module-actions">
        <button class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2" 
            style="border-radius: 10px; font-weight: 600;" onclick="addMiembro()">
            <i class="fas fa-user-plus"></i> Nuevo Miembro
        </button>
    </div>
</div>

<!-- Contenedor Principal del Módulo -->
<div class="card-module">
    <?php if (isset($_GET['familia_id'])): ?>
    <div class="px-4 pt-4">
        <div class="alert alert-primary d-flex align-items-center justify-content-between py-2 mb-0 border-0 shadow-sm" 
             style="background-color: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); border-radius: 12px;">
            <div>
                <i class="fas fa-filter me-2"></i> 
                Mostrando únicamente los miembros de la familia seleccionada.
            </div>
            <a href="/ProyectoIglesia/miembros" class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3">
                <i class="fas fa-times me-1"></i> Limpiar Filtro
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="card-header-custom">
        <div class="d-flex align-items-center gap-3 w-100">
            <div class="flex-grow-1" style="max-width: 500px;">
                <div class="search-bar-premium d-flex align-items-center w-100" style="max-width: 450px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, DPI, ciudad o familia..." autocomplete="off">
                    <button class="btn btn-link text-muted p-2 border-0 clear-search" type="button" id="clearSearch" style="display: none;">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                    <i class="fas fa-users me-1"></i> <span id="miembroCount">0</span> Miembros
                </span>
            </div>
        </div>
    </div>
    
    <div class="table-responsive w-100">
        <table class="table-custom" id="miembrosTable">
            <thead>
                <tr>
                    <th style="width: 30%;">Miembro</th>
                    <th style="width: 15%;">DPI / Identidad</th>
                    <th style="width: 15%;">Contacto</th>
                    <th style="width: 15%;">Ubicación</th>
                    <th style="width: 10%; text-align: center;">Estado</th>
                    <th style="width: 15%; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="miembrosTableBody">
                <!-- Se rellena dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar/editar miembro -->
<div class="modal fade" id="miembroModal" tabindex="-1" aria-labelledby="miembroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="miembroModalLabel">
                    <i class="fas fa-user-plus text-primary me-2"></i>Nuevo Miembro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="miembroForm">
                    <input type="hidden" id="miembro_id" name="miembro_id">

                    <div class="d-flex flex-column flex-md-row gap-4 mb-4 align-items-center align-items-md-start">
                        <div class="avatar-upload-wrapper">
                            <div class="avatar-preview-container shadow-sm">
                                <img id="avatar_preview" src="/ProyectoIglesia/assets/img/miembros/default_avatar.png" alt="Avatar Preview">
                                <div class="avatar-overlay" onclick="document.getElementById('foto_input').click()">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-camera mb-1"></i>
                                        <span style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase;">Cambiar</span>
                                    </div>
                                </div>
                            </div>
                            <input type="file" name="foto" id="foto_input" hidden accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="flex-grow-1 w-100">
                            <div class="alert d-flex align-items-center border-0 rounded-3 mb-3" style="background-color: rgba(var(--bs-info-rgb), 0.1); color: var(--bs-info); font-size: 0.85rem;">
                                <i class="fas fa-info-circle me-3 fs-5"></i>
                                <span>Los campos marcados con un asterisco (<span class="text-danger fw-bold">*</span>) son <strong>obligatorios</strong>.</span>
                            </div>
                            
                            <div class="form-section-title text-primary fw-bold text-uppercase border-bottom pb-2 mb-3 mt-0" style="font-size: 0.75rem; letter-spacing: 0.5px; border-color: rgba(255,255,255,0.05) !important;">Información Personal</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label fw-bold text-muted small text-uppercase">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombres" name="nombres" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidos" class="form-label fw-bold text-muted small text-uppercase">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="no_dpi" class="form-label fw-bold text-muted small text-uppercase">DPI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_dpi" name="no_dpi" maxlength="13" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="form-label fw-bold text-muted small text-uppercase">Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        <div class="col-md-4">
                            <label for="sexo" class="form-label fw-bold text-muted small text-uppercase">Sexo <span class="text-danger">*</span></label>
                            <select class="form-select select2-init" id="sexo" name="sexo" required>
                                <option value="">-- Selecciona --</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="estado_civil" class="form-label fw-bold text-muted small text-uppercase">Estado Civil <span class="text-danger">*</span></label>
                            <select class="form-select select2-init" id="estado_civil" name="estado_civil" required>
                                <option value="">-- Selecciona --</option>
                                <option value="Soltero (a)">Soltero (a)</option>
                                <option value="Casado (a)">Casado (a)</option>
                                <option value="Unido (a)">Unido (a)</option>
                                <option value="Divorciado (a)">Divorciado (a)</option>
                                <option value="Viudo (a)">Viudo (a)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section-title text-primary fw-bold text-uppercase border-bottom pb-2 mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px; border-color: rgba(255,255,255,0.05) !important;">Contacto y Ubicación</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="direccion" class="form-label fw-bold text-muted small text-uppercase">Dirección <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ciudad" class="form-label fw-bold text-muted small text-uppercase">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="tel_celular" class="form-label fw-bold text-muted small text-uppercase">Celular <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="tel_celular" name="tel_celular" maxlength="8" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tel_fijo" class="form-label fw-bold text-muted small text-uppercase">Tel. Fijo</label>
                            <input type="tel" class="form-control" id="tel_fijo" name="tel_fijo" maxlength="8">
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-bold text-muted small text-uppercase">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>

                    <div class="form-section-title text-primary fw-bold text-uppercase border-bottom pb-2 mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px; border-color: rgba(255,255,255,0.05) !important;">Iglesia y Ministerio</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="familia" class="form-label fw-bold text-muted small text-uppercase">Familia <span class="text-danger">*</span></label>
                            <select class="form-select select2-init" id="familia" name="familia" required>
                                <option value="">-- Selecciona --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="nivel_estudio" class="form-label fw-bold text-muted small text-uppercase">Estudios</label>
                            <select class="form-select select2-init" id="nivel_estudio" name="nivel_estudio">
                                <option value="">-- Selecciona --</option>
                                <option value="Sin Estudios">Sin Estudios</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Basicos">Basicos</option>
                                <option value="Diversificado">Diversificado</option>
                                <option value="Universitario">Universitario</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="profesion" class="form-label fw-bold text-muted small text-uppercase">Profesión</label>
                            <input type="text" class="form-control" id="profesion" name="profesion" placeholder="Ej. Ingeniero, Comerciante">
                        </div>
                        <div class="col-md-4">
                            <label for="cargo" class="form-label fw-bold text-muted small text-uppercase">Ministerio</label>
                            <select class="form-select select2-init" id="cargo" name="cargo">
                                <option value="">-- Ninguno --</option>
                                <option value="Pastor">Pastor</option>
                                <option value="Lider">Líder</option>
                                <option value="Músico">Músico</option>
                                <option value="Ujier">Ujier</option>
                                <option value="Maestro">Maestro (a)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label fw-bold text-muted small text-uppercase">Estado <span class="text-danger">*</span></label>
                        <select class="form-select select2-init" id="estado" name="estado" required>
                            <option value="Activo">Activo (Congregándose)</option>
                            <option value="Inactivo">Inactivo (Ausente o Traslado)</option>
                        </select>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-dark px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm"><i class="fas fa-save me-2"></i>Guardar Miembro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Perfil de Miembro -->
<div class="modal fade" id="modalPerfilMiembro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-0">
                <!-- Cover / Header -->
                <div class="position-relative" style="height: 120px; background: linear-gradient(135deg, var(--bs-primary), #1e40af);">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!-- Profile Picture and Basic Info -->
                <div class="text-center" style="margin-top: -60px;">
                    <div class="position-relative d-inline-block">
                        <img id="perfil_foto" src="/ProyectoIglesia/assets/img/miembros/default_avatar.png" 
                            class="rounded-circle border border-4 border-white shadow" 
                            style="width: 120px; height: 120px; object-fit: cover; background-color: white;">
                        <span id="perfil_estado_badge" class="position-absolute bottom-0 end-0 badge rounded-pill border border-2 border-white" style="padding: 0.5rem 0.8rem;">Activo</span>
                    </div>
                    <h4 id="perfil_nombre" class="mt-3 mb-1 fw-bold text-dark">Nombre del Miembro</h4>
                    <p id="perfil_cargo" class="text-primary fw-bold small text-uppercase mb-0">Ministerio / Cargo</p>
                    <p class="text-muted small mb-4">ID: <span id="perfil_id_display" class="fw-bold text-primary">#0000</span></p>
                </div>

                <!-- Profile Details Grid -->
                <div class="px-4 pb-4">
                    <div class="row g-4 text-start">
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">DPI / Identidad</label>
                            <span id="perfil_dpi" class="text-dark fw-bold">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Teléfono</label>
                            <span id="perfil_telefono" class="text-dark fw-bold">-</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Dirección</label>
                            <span id="perfil_direccion" class="text-dark fw-bold">-</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Familia</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold small" id="perfil_familia">
                                    Sin Familia
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-5 d-grid">
                        <a id="btn_descargar_carnet" href="#" target="_blank" class="btn btn-primary py-3 rounded-pill shadow-sm fw-bold">
                            <i class="fas fa-id-card me-2"></i> Descargar Carnet PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function() {
    // Definición de funciones globales para uso en HTML (onclick)
    window.addMiembro = function() {
        const form = document.getElementById('miembroForm');
        const $familia = $('#familia');
        if(form) form.reset();
        document.getElementById('miembro_id').value = '';
        
        // Reset de todos los Select2
        $('#familia, #sexo, #estado_civil, #nivel_estudio, #cargo, #estado').val(null).trigger('change');
        
        document.getElementById('avatar_preview').src = '/ProyectoIglesia/assets/img/miembros/default_avatar.png';
        document.getElementById('foto_input').value = '';
        
        document.getElementById('miembroModalLabel').textContent = 'Nuevo Miembro';
        const modal = bootstrap.Modal.getInstance(document.getElementById('miembroModal')) || new bootstrap.Modal(document.getElementById('miembroModal'));
        modal.show();
    };

    // Esperar a que el DOM esté listo
    document.addEventListener("DOMContentLoaded", function() {
        const tableBody = document.getElementById('miembrosTableBody');
        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearch');
        const miembroCount = document.getElementById('miembroCount');
        const miembroForm = document.getElementById('miembroForm');
        const $familiaSelect = $('#familia');

        // Inicializar Select2 para todos los selectores del modal
        function initSelect2() {
            if (typeof $.fn.select2 === 'undefined') return;
            
            // Configuración base para todos los selects
            const commonConfig = {
                theme: 'bootstrap-5',
                width: '100%'
            };

            $('#familia').select2({
                ...commonConfig,
                placeholder: 'Buscar familia...',
                language: { noResults: () => "No se encontraron familias", searching: () => "Buscando..." }
            });

            $('#sexo').select2({ ...commonConfig, placeholder: 'Selecciona sexo' });
            $('#estado_civil').select2({ ...commonConfig, placeholder: 'Selecciona estado civil' });
            $('#nivel_estudio').select2({ ...commonConfig, placeholder: 'Selecciona nivel de estudios' });
            $('#cargo').select2({ ...commonConfig, placeholder: 'Selecciona cargo' });
            $('#estado').select2({ ...commonConfig, placeholder: 'Selecciona estado' });
        }

        // Re-inicializar Select2 al abrir el modal para asegurar posicionamiento correcto
        $('#miembroModal').on('shown.bs.modal', function() {
            initSelect2();
        });

        // Preview de imagen en tiempo real
        const fotoInput = document.getElementById('foto_input');
        const avatarPreview = document.getElementById('avatar_preview');
        if (fotoInput && avatarPreview) {
            fotoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => avatarPreview.src = e.target.result;
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        function loadMiembros() {
            const urlParams = new URLSearchParams(window.location.search);
            const familiaId = urlParams.get('familia_id');
            const endpoint = familiaId ? `/ProyectoIglesia/_/m?familia_id=${familiaId}` : '/ProyectoIglesia/_/m';

            fetch(endpoint)
                .then(res => res.json())
                .then(res => {
                    if(res.status === 'success') {
                        renderMiembros(res.data);
                    }
                })
                .catch(err => console.error("Error al cargar:", err));
        }

        function renderMiembros(miembros) {
            if (!tableBody) return;
            tableBody.innerHTML = '';
            if(miembroCount) miembroCount.textContent = miembros.length;

            if (!miembros || miembros.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">No se encontraron resultados.</td></tr>`;
                return;
            }

            miembros.forEach(m => {
                const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1'];
                const initial = (m.nombres || '?').substring(0, 1).toUpperCase();
                const avatarColor = colors[m.miembro_id % colors.length] || '#ccc';

                const photoSrc = m.foto && m.foto !== 'default_avatar.png' 
                    ? `/ProyectoIglesia/assets/img/miembros/${m.foto}` 
                    : null;
                
                const avatarHtml = photoSrc 
                    ? `<img src="${photoSrc}" class="rounded-circle me-3 shadow-sm border border-2 border-white" 
                        style="width: 42px; height: 42px; object-fit: cover;">`
                    : `<div class="text-white rounded-circle me-3 d-flex justify-content-center align-items-center shadow-sm"
                            style="width: 42px; height: 42px; font-weight: bold; background-color: ${avatarColor}; border: 2px solid white;">
                            ${initial}
                        </div>`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center py-1">
                            ${avatarHtml}
                            <div>
                                <div class="fw-bold text-dark">${m.nombres} ${m.apellidos}</div>
                                <div class="small text-muted">ID: #${String(m.miembro_id).padStart(4, '0')}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">${m.no_dpi}</td>
                    <td>
                        <div class="text-dark small"><i class="fas fa-mobile-alt me-1 text-primary opacity-50"></i>${m.tel_celular}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">${m.email || '—'}</div>
                    </td>
                    <td class="text-muted small">
                        <div>${m.ciudad || '—'}</div>
                        <div style="font-size: 0.75rem;">${m.direccion || ''}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge ${m.estado === 'Activo' ? 'bg-success' : 'bg-secondary'} bg-opacity-10 ${m.estado === 'Activo' ? 'text-success' : 'text-secondary'} rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                            ${m.estado}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-light text-info border-0 btn-view" title="Ver Perfil"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-light text-primary border-0 btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger border-0 btn-delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                `;

                row.querySelector('.btn-view').onclick = () => verPerfil(m);
                row.querySelector('.btn-edit').onclick = () => editMiembro(m);
                row.querySelector('.btn-delete').onclick = () => deleteMiembro(m.miembro_id);
                tableBody.appendChild(row);
            });
        }

        window.editMiembro = function(m) {
            document.getElementById('miembroModalLabel').textContent = 'Editar Miembro';
            document.getElementById('miembro_id').value = m.miembro_id;
            document.getElementById('nombres').value = m.nombres;
            document.getElementById('apellidos').value = m.apellidos;
            document.getElementById('no_dpi').value = m.no_dpi;
            document.getElementById('fecha_nacimiento').value = m.fecha_nacimiento;
            document.getElementById('sexo').value = m.sexo;
            document.getElementById('estado_civil').value = m.estado_civil;
            document.getElementById('direccion').value = m.direccion;
            document.getElementById('ciudad').value = m.ciudad;
            document.getElementById('tel_celular').value = m.tel_celular;
            document.getElementById('tel_fijo').value = m.tel_fijo;
            document.getElementById('email').value = m.email;
            $('#familia').val(m.familia).trigger('change');
            $('#sexo').val(m.sexo).trigger('change');
            $('#estado_civil').val(m.estado_civil).trigger('change');
            $('#nivel_estudio').val(m.nivel_estudio).trigger('change');
            document.getElementById('profesion').value = m.profesion || '';
            $('#cargo').val(m.cargo).trigger('change');
            $('#estado').val(m.estado).trigger('change');

            // Cargar foto actual en el preview
            const avatarPreview = document.getElementById('avatar_preview');
            avatarPreview.src = m.foto && m.foto !== 'default_avatar.png' 
                ? `/ProyectoIglesia/assets/img/miembros/${m.foto}` 
                : '/ProyectoIglesia/assets/img/miembros/default_avatar.png';
            document.getElementById('foto_input').value = '';

            const modal = bootstrap.Modal.getInstance(document.getElementById('miembroModal')) || new bootstrap.Modal(document.getElementById('miembroModal'));
            modal.show();
        };

        window.verPerfil = function(m) {
            document.getElementById('perfil_foto').src = m.foto && m.foto !== 'default_avatar.png' 
                ? `/ProyectoIglesia/assets/img/miembros/${m.foto}` 
                : '/ProyectoIglesia/assets/img/miembros/default_avatar.png';
            
            document.getElementById('perfil_nombre').textContent = `${m.nombres} ${m.apellidos}`;
            document.getElementById('perfil_cargo').textContent = m.cargo || 'General';
            document.getElementById('perfil_id_display').textContent = `#${String(m.miembro_id).padStart(4, '0')}`;
            document.getElementById('perfil_dpi').textContent = m.no_dpi;
            document.getElementById('perfil_telefono').textContent = m.tel_celular;
            document.getElementById('perfil_direccion').textContent = m.direccion;
            document.getElementById('perfil_familia').textContent = m.nombre_familia || 'Sin Familia';
            
            const badge = document.getElementById('perfil_estado_badge');
            badge.textContent = m.estado;
            badge.className = `position-absolute bottom-0 end-0 badge rounded-pill border border-2 border-white ${m.estado === 'Activo' ? 'bg-success' : 'bg-secondary'}`;
            
            document.getElementById('btn_descargar_carnet').href = `/ProyectoIglesia/_/m/carnet?id=${m.miembro_id}`;

            const modal = new bootstrap.Modal(document.getElementById('modalPerfilMiembro'));
            modal.show();
        };

        if(miembroForm) {
            miembroForm.onsubmit = function(e) {
                e.preventDefault();
                console.log("Submitting form...");
                const formData = new FormData(this);
                
                fetch('/ProyectoIglesia/_/m/save', { 
                    method: 'POST', 
                    body: formData 
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch(err) {
                        console.error("Invalid JSON response:", text);
                        throw new Error("El servidor devolvió una respuesta no válida.");
                    }
                })
                .then(res => {
                    if(res.status === 'success') {
                        const modalEl = document.getElementById('miembroModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                        
                        loadMiembros();
                        Swal.fire('¡Éxito!', res.message, 'success');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                    Swal.fire('Error', err.message || 'No se pudo procesar la solicitud.', 'error');
                });
            };
        }

        window.deleteMiembro = function(id) {
            Swal.fire({
                title: '¿Eliminar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then(r => {
                if(r.isConfirmed) {
                    const fd = new FormData(); fd.append('miembro_id', id);
                    fetch('/ProyectoIglesia/_/m/del', { method: 'POST', body: fd })
                        .then(res => res.json())
                        .then(res => {
                            if(res.status === 'success') { loadMiembros(); Swal.fire('Eliminado', res.message, 'success'); }
                        });
                }
            });
        };

        // BÚSQUEDA
        let timer;
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value;
                if(clearBtn) clearBtn.style.display = term.length > 0 ? 'block' : 'none';
                
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const urlParams = new URLSearchParams(window.location.search);
                    const fId = urlParams.get('familia_id');
                    let url = `/ProyectoIglesia/_/m?q=${encodeURIComponent(term)}`;
                    if(fId) url += `&familia_id=${fId}`;

                    console.log("Fetching search results from:", url);

                    fetch(url)
                        .then(res => res.json())
                        .then(res => {
                            console.log("Search response received:", res);
                            if(res.status === 'success') {
                                console.log("Rendering", res.data.length, "results");
                                renderMiembros(res.data);
                            }
                        });
                }, 300);
            });
        }

        if(clearBtn) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                clearBtn.style.display = 'none';
                loadMiembros();
                searchInput.focus();
            });
        }

        // Re-inicializar Select2 al abrir el modal para asegurar posicionamiento correcto
        $('#miembroModal').on('shown.bs.modal', function() {
            initSelect2();
        });

        // Cargar Familias
        fetch('/ProyectoIglesia/_/f')
            .then(res => res.json())
            .then(res => {
                $familiaSelect.empty().append('<option value="">-- Selecciona --</option>');
                if(res.status === 'success' && Array.isArray(res.data)) {
                    res.data.forEach(f => $familiaSelect.append(new Option(f.nombre, f.id)));
                }
                initSelect2();
            });

        loadMiembros();
    });
})();
</script>
