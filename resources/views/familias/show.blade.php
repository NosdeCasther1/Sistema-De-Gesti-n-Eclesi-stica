@extends('layouts.app')

@section('title', 'Detalle de Familia - AD Rey de Reyes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="fas fa-home text-primary me-2"></i>{{ $familia->nombre }}</h2>
        <p class="text-muted small mb-0">Información detallada y lista de integrantes del núcleo familiar.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('familias.edit', $familia->id) }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-pen"></i><span>Editar Familia</span>
        </a>
        <a href="{{ route('familias.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Tarjeta de Información General --}}
    <div class="col-md-5">
        <div class="card-module p-4 h-100 shadow-sm bento-card">
            <h4 class="fw-bold mb-4 pb-2 border-bottom"><i class="fas fa-info-circle text-primary me-2"></i>Información General</h4>
            
            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Dirección de Domicilio</label>
                <div class="d-flex align-items-start gap-2 fs-5 text-white">
                    <i class="fas fa-map-marker-alt text-danger mt-1"></i>
                    <span>{{ $familia->direccion ?? 'Sin dirección registrada' }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Teléfono Principal</label>
                <div class="d-flex align-items-center gap-2 fs-5 text-info">
                    <i class="fas fa-phone"></i>
                    <span>{{ $familia->telefono_principal ?? 'Sin teléfono registrado' }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Célula Asignada</label>
                <div class="d-flex align-items-center gap-2 fs-5 text-white">
                    <i class="fas fa-users text-warning"></i>
                    <span>{{ $familia->celula->nombre ?? 'Ninguna célula asignada' }}</span>
                </div>
            </div>

            <div class="mb-3">
                <label class="text-muted small text-uppercase fw-bold d-block mb-1">Notas / Observaciones</label>
                <p class="text-white-50 bg-dark bg-opacity-25 p-3 rounded-3 mb-0" style="font-size: 0.95rem;">
                    {{ $familia->notas ?: 'No hay observaciones adicionales para esta familia.' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tarjeta de Integrantes --}}
    <div class="col-md-7">
        <div class="card-module p-4 h-100 shadow-sm bento-card d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold mb-0"><i class="fas fa-users text-primary me-2"></i>Integrantes de la Familia</h4>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fs-6 fw-bold">
                    {{ $familia->miembros->count() }} {{ $familia->miembros->count() == 1 ? 'Miembro' : 'Miembros' }}
                </span>
            </div>

            <div class="flex-grow-1 overflow-auto custom-table-scroll" style="max-height: 420px;">
                @if($familia->miembros->count() > 0)
                    <div class="list-group list-group-flush gap-2">
                        @foreach($familia->miembros as $miembro)
                            <a href="{{ route('miembros.show', $miembro->id) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 rounded-3 bg-dark bg-opacity-25 border-0 mb-2 transition-all hover-scale">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $miembro->foto ? asset('storage/miembros/' . $miembro->foto) : asset('assets/img/default_avatar.png') }}" 
                                         class="rounded-circle object-fit-cover border border-2 border-secondary" 
                                         style="width: 48px; height: 48px;">
                                    <div>
                                        <h6 class="fw-bold text-white mb-1">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h6>
                                        <div class="text-muted small d-flex align-items-center gap-3">
                                            <span><i class="fas fa-id-card me-1"></i>{{ $miembro->dpi ?? 'Sin DPI' }}</span>
                                            <span><i class="fas fa-church me-1"></i>{{ $miembro->ministerio ?? 'Sin ministerio' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-1 rounded-pill">
                                        {{ $miembro->etapa_consolidacion }}
                                    </span>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                        <h5 class="fw-bold mb-1">Sin integrantes registrados</h5>
                        <p class="small mb-0">Aún no se han asignado miembros a este núcleo familiar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
