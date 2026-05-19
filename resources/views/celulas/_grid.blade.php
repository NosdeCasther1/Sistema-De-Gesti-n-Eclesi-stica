<div class="row g-4" id="celulasGrid">
    @forelse($celulas as $c)
    <div class="col-md-4">
        <div class="card-module p-4 h-100 position-relative overflow-hidden" style="border-top: 3px solid var(--bs-primary);">
            {{-- Badge de Sector --}}
            <div class="position-absolute top-0 end-0 p-3">
                <span class="badge rounded-pill px-3"
                      style="background:rgba(255,193,7,.15);color:#ffc107;">
                    {{ $c->sector ?? 'Sin Sector' }}
                </span>
            </div>

            <div class="mb-3 pe-5">
                <h5 class="fw-bold mb-1">{{ $c->nombre }}</h5>
                <div class="text-muted small d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-day"></i>
                    {{ $c->dia_reunion }} a las {{ \Carbon\Carbon::parse($c->hora_reunion)->format('H:i') }}
                </div>
            </div>

            <div class="d-flex align-items-center mb-4 p-3 rounded-3" style="background:rgba(var(--bs-primary-rgb),.05);">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3"
                     style="width:40px;height:40px;background:var(--bs-primary);flex-shrink:0;">
                    {{ strtoupper(substr($c->lider->nombres ?? '?', 0, 1)) }}
                </div>
                <div>
                    <div class="text-muted" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Líder de Célula</div>
                    <div class="fw-semibold">{{ $c->lider->nombres ?? 'Sin Líder' }} {{ $c->lider->apellidos ?? '' }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="h5 mb-0 fw-bold" style="color:var(--bs-primary);">{{ $c->miembros_count }}</div>
                    <div class="text-muted small">Integrantes</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('celulas.show', $c->id) }}"
                       class="btn btn-sm btn-outline-info rounded-circle" title="Ver Detalles" style="width:34px;height:34px;">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('celulas.edit', $c->id) }}"
                       class="btn btn-sm btn-outline-secondary rounded-circle" title="Editar" style="width:34px;height:34px;">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="fas fa-network-wired fa-3x mb-3 d-block opacity-25"></i>
        <h5>No se encontraron células con ese criterio.</h5>
    </div>
    @endforelse
</div>

@if($celulas->hasPages())
<div class="mt-4">
    {{ $celulas->appends(request()->query())->links() }}
</div>
@endif
