@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold text-white">Perfil del Miembro</h1>
        <a href="{{ route('miembros.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-module text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ $miembro->foto ? asset('storage/miembros/' . $miembro->foto) : asset('assets/img/default_avatar.png') }}" 
                         class="rounded-circle border border-3 border-primary shadow" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <h4 class="fw-bold mb-1 text-white">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h4>
                <p class="text-muted mb-3">{{ $miembro->ministerio ?? 'Miembro' }}</p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('miembros.carnet', $miembro->id) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-id-card me-2"></i>Descargar Carnet
                    </a>
                    <a href="{{ route('miembros.edit', $miembro->id) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-2"></i>Editar Datos
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card-module p-4">
                <h5 class="text-primary fw-bold text-uppercase border-bottom border-secondary border-opacity-25 pb-2 mb-4" style="font-size: 0.8rem;">
                    Información Personal y Contacto
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">DPI / Identidad</label>
                        <span class="fs-5 text-white">{{ $miembro->dpi ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Fecha de Nacimiento</label>
                        <span class="fs-5 text-white">{{ $miembro->fecha_nacimiento ? $miembro->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Sexo</label>
                        <span class="fs-5 text-white">{{ $miembro->sexo === 'M' ? 'Masculino' : ($miembro->sexo === 'F' ? 'Femenino' : 'N/A') }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Estado Civil</label>
                        <span class="fs-5 text-white">{{ $miembro->estado_civil ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Teléfono</label>
                        <span class="fs-5 text-info">{{ $miembro->telefono ?? 'Sin teléfono' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Correo Electrónico</label>
                        <span class="fs-5 text-white">{{ $miembro->email ?? 'Sin correo' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Familia</label>
                        <span class="fs-5 text-white">{{ $miembro->familia->nombre ?? 'Sin familia asignada' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Fecha de Integración</label>
                        <span class="fs-5 text-white">{{ $miembro->fecha_integracion ? $miembro->fecha_integracion->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small text-uppercase fw-bold d-block">Dirección Residencial</label>
                        <span class="fs-5 text-white">{{ $miembro->direccion ?? 'N/A' }}, {{ $miembro->ciudad ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small text-uppercase fw-bold d-block">Nivel Académico</label>
                        <span class="fs-5 text-white">{{ $miembro->nivel_academico ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small text-uppercase fw-bold d-block">Profesión / Oficio</label>
                        <span class="fs-5 text-white">{{ $miembro->profesion ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small text-uppercase fw-bold d-block">Lugar de Trabajo/Estudio</label>
                        <span class="fs-5 text-white">{{ $miembro->lugar_trabajo_estudio ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Estado de Consolidación</label>
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 fs-6 rounded-pill">
                            {{ $miembro->etapa_consolidacion }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
