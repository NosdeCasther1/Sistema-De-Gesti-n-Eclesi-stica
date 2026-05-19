<!-- Header del Módulo -->
<div class="module-header">
    <div class="module-title-section">
        <ul class="breadcrumb-custom mb-2">
            <li class="breadcrumb-item-custom"><a href="/ProyectoIglesia/inicio">Inicio</a></li>
            <li class="breadcrumb-item-custom active">Gestión de Familias</li>
        </ul>
        <h1 class="h2 text-dark font-weight-bold">Familias</h1>
        <p class="text-muted mb-0">Organiza y agrupa a los miembros por núcleos familiares.</p>
    </div>
    <div class="module-actions">
        <button class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center gap-2" 
            style="border-radius: 10px; font-weight: 600;" onclick="addFamilia()">
            <i class="fas fa-plus"></i> Nueva Familia
        </button>
    </div>
</div>

<div class="card-module p-4">
    <div class="mb-4" style="max-width: 500px;">
        <div class="search-bar-premium d-flex align-items-center w-100" style="max-width: 450px;">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar familia por nombre o descripción..." autocomplete="off">
            <button class="btn btn-link text-muted p-2 border-0" type="button" id="clearSearch" style="display: none;">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
    </div>

    <div class="row" id="familiasContainer">
        <!-- Contenedor de Skeletons (se llena vía JS) -->
    </div>
</div>

<!-- Modal Familia -->
<div class="modal fade" id="familiaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header pt-4 px-4 border-bottom-0">
                <h5 class="modal-title fw-bold" id="familiaModalLabel">Nueva Familia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form id="familiaForm">
                    <input type="hidden" id="familia_id" name="id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nombre" class="form-label fw-bold text-muted small text-uppercase">Nombre de la Familia</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Familia Pérez" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono_principal" class="form-label fw-bold text-muted small text-uppercase">Teléfono Principal</label>
                            <input type="text" class="form-control" id="telefono_principal" name="telefono_principal" placeholder="Ej. 1234-5678">
                        </div>
                        <div class="col-md-6">
                            <label for="celula_id" class="form-label fw-bold text-muted small text-uppercase">Célula Asignada</label>
                            <select class="form-select" id="celula_id" name="celula_id">
                                <option value="">— Ninguna —</option>
                                <option value="1">Célula Central</option>
                                <option value="2">Célula Norte</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="direccion" class="form-label fw-bold text-muted small text-uppercase">Dirección Exacta</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Calle, Avenida, Zona...">
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label fw-bold text-muted small text-uppercase">Descripción / Notas</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Información adicional relevante..."></textarea>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('familiasContainer');
    const modal = new bootstrap.Modal(document.getElementById('familiaModal'));
    const form = document.getElementById('familiaForm');
    const searchInput = document.getElementById('searchInput');
    let allFamilias = [];

    function loadFamilias() {
        mostrarSkeleton('familiasContainer', 6); // Mostrar 6 skeletons antes del fetch
        fetch('/ProyectoIglesia/_/f')
            .then(res => res.json())
            .then(res => {
                ocultarSkeleton('familiasContainer');
                if (res.status === 'success') {
                    allFamilias = res.data;
                    renderFamilias(allFamilias);
                }
            });
    }

    // Búsqueda
    searchInput.addEventListener('input', function() {
        const clearBtn = document.getElementById('clearSearch');
        if (this.value.length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }

        const term = this.value.toLowerCase();
        const filtered = allFamilias.filter(f => 
            f.nombre.toLowerCase().includes(term) || 
            (f.descripcion && f.descripcion.toLowerCase().includes(term))
        );
        renderFamilias(filtered);
    });

    document.getElementById('clearSearch').addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        renderFamilias(allFamilias);
        searchInput.focus();
    });

    function renderFamilias(familias) {
        if (familias.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-5 text-muted">No hay familias registradas.</div>';
            return;
        }

        container.innerHTML = familias.map(f => `
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; transition: transform 0.2s;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><a class="dropdown-item" href="#" onclick="editFamilia(${JSON.stringify(f).replace(/"/g, '&quot;')})"><i class="fas fa-edit me-2"></i>Editar</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteFamilia(${f.id})"><i class="fas fa-trash me-2"></i>Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">${f.nombre}</h5>
                        <p class="text-muted small mb-1"><i class="fas fa-phone-alt me-1"></i> ${f.telefono_principal || '—'}</p>
                        <p class="text-muted small mb-3 text-truncate" title="${f.direccion || ''}"><i class="fas fa-map-marker-alt me-1"></i> ${f.direccion || 'Sin dirección'}</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            ${f.total_integrantes > 0 
                                ? `<span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    <i class="fas fa-user-friends me-1"></i> ${f.total_integrantes} Integrantes
                                   </span>`
                                : `<span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                    <i class="fas fa-user-slash me-1"></i> Sin integrantes asignados
                                   </span>`
                            }
                            <a href="/ProyectoIglesia/miembros?familia_id=${f.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Ver Miembros</a>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    window.addFamilia = function() {
        form.reset();
        document.getElementById('familia_id').value = '';
        document.getElementById('celula_id').value = '';
        document.getElementById('familiaModalLabel').textContent = 'Nueva Familia';
        modal.show();
    };

    window.editFamilia = function(f) {
        document.getElementById('familiaModalLabel').textContent = 'Editar Familia';
        document.getElementById('familia_id').value = f.id;
        document.getElementById('nombre').value = f.nombre;
        document.getElementById('telefono_principal').value = f.telefono_principal || '';
        document.getElementById('celula_id').value = f.celula_id || '';
        document.getElementById('direccion').value = f.direccion || '';
        document.getElementById('descripcion').value = f.descripcion;
        modal.show();
    };

    form.onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/ProyectoIglesia/_/f/save', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    modal.hide();
                    loadFamilias();
                    Swal.fire('¡Hecho!', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
    };

    window.deleteFamilia = function(id) {
        Swal.fire({
            title: '¿Eliminar familia?',
            text: "Esta acción no se puede deshacer si tiene miembros asociados.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                fetch('/ProyectoIglesia/_/f/del', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            loadFamilias();
                            Swal.fire('Eliminado', res.message, 'success');
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    });
            }
        });
    };

    loadFamilias();
});
</script>
