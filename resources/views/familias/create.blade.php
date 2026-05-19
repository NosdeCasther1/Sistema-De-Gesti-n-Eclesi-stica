@extends('layouts.app')

@section('title', 'Nueva Familia - AD Rey de Reyes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Nueva Familia</h2>
        <p class="text-muted small mb-0">Defina el nombre del núcleo familiar para agrupar a sus miembros.</p>
    </div>
    <a href="{{ route('familias.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card-module p-4" style="max-width: 600px;">
    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('familias.store') }}" method="POST">
        @csrf

        <div class="form-section-title"><i class="fas fa-home"></i> Datos del Núcleo Familiar</div>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label">Nombre de la Familia *</label>
                <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre') }}" placeholder="Ej: Familia Pérez García" required>
                <div class="form-text">Sugerencia: Use los apellidos de los padres o el nombre del titular.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono Principal</label>
                <input type="text" name="telefono_principal" class="form-control" value="{{ old('telefono_principal') }}" placeholder="Ej: 5555-1234">
            </div>
            <div class="col-md-6">
                <label class="form-label">Célula Familiar</label>
                <select name="celula_id" class="form-select">
                    <option value="">Seleccionar Célula (Opcional)</option>
                    @foreach($celulas as $celula)
                        <option value="{{ $celula->id }}" {{ old('celula_id') == $celula->id ? 'selected' : '' }}>{{ $celula->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Dirección de Domicilio</label>
                <textarea name="direccion" class="form-control" rows="2" placeholder="Calle, Avenida, Zona, Municipio...">{{ old('direccion') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Notas / Observaciones</label>
                <textarea name="notas" class="form-control" rows="2" placeholder="Información adicional relevante sobre la familia...">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="pt-3 border-top" style="border-color: var(--border-color) !important;">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                <i class="fas fa-save me-2"></i>Crear Familia
            </button>
            <a href="{{ route('familias.index') }}" class="btn btn-outline-secondary px-4 ms-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
