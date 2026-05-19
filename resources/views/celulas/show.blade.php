@extends('layouts.app')

@section('title', 'Detalle de Célula - AD Rey de Reyes')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('celulas.index') }}" class="text-decoration-none">Células</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $celula->nombre }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-white mb-0">{{ $celula->nombre }}</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('asistencia.scanner', ['celula_id' => $celula->id]) }}" class="btn btn-primary px-4 shadow">
                <i class="fas fa-qrcode me-2"></i> Asistencia QR
            </a>
            <a href="{{ route('asistencia.manual', ['celula_id' => $celula->id]) }}" class="btn btn-warning px-4 shadow">
                <i class="fas fa-edit me-2"></i> Toma Manual
            </a>
            <a href="{{ route('reportes.asistencia_celula', $celula->id) }}" target="_blank" class="btn btn-outline-info">
                <i class="fas fa-file-invoice me-2"></i> Reporte Mensual
            </a>
            <a href="{{ route('celulas.edit', $celula->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Info de la Célula -->
        <div class="col-md-4">
            <div class="card-module p-4 mb-4">
                <h5 class="fw-bold mb-4 text-primary">Información General</h5>
                
                <div class="mb-3">
                    <label class="smaller text-muted text-uppercase fw-bold d-block">Líder</label>
                    <div class="fw-bold text-white fs-5">{{ $celula->lider->nombres ?? 'Sin Líder' }} {{ $celula->lider->apellidos ?? '' }}</div>
                    @if($celula->lider)
                        <div class="small text-muted"><i class="fas fa-phone me-1"></i> {{ $celula->lider->telefono }}</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="smaller text-muted text-uppercase fw-bold d-block">Día y Hora</label>
                    <div class="text-white fw-bold">{{ $celula->dia_reunion }} a las {{ \Carbon\Carbon::parse($celula->hora_reunion)->format('H:i') }}</div>
                </div>

                <div class="mb-3">
                    <label class="smaller text-muted text-uppercase fw-bold d-block">Ubicación</label>
                    <div class="text-white small">{{ $celula->direccion ?? 'Sin dirección' }}</div>
                    <div class="badge bg-warning bg-opacity-10 text-warning mt-2">{{ $celula->sector }}</div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="card-module p-4 bg-primary bg-opacity-10 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h3 mb-0 fw-bold text-primary">{{ $celula->miembros_count }}</div>
                        <div class="text-muted small fw-bold">Miembros Integrados</div>
                    </div>
                    <i class="fas fa-users fs-1 text-primary opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Listado de Integrantes -->
        <div class="col-md-8">
            <div class="card-module p-0 overflow-hidden">
                <div class="p-4 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Integrantes de la Célula</h5>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Agregar Miembro
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-dark mb-0">
                        <thead>
                            <tr class="bg-black bg-opacity-25">
                                <th class="ps-4 py-3 text-muted smaller text-uppercase">Miembro</th>
                                <th class="py-3 text-muted smaller text-uppercase">Teléfono</th>
                                <th class="py-3 text-muted smaller text-uppercase text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($celula->miembros as $m)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 35px; height: 35px;">
                                            {{ substr($m->nombres, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white small">{{ $m->nombres }} {{ $m->apellidos }}</div>
                                            <div class="smaller text-muted">{{ $m->ministerio }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $m->telefono }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('miembros.show', $m->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">No hay miembros asignados a esta célula.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
