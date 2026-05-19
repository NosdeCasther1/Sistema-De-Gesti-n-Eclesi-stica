@extends('layouts.app')

@section('title', 'Crear Célula - AD Rey de Reyes')

@section('content')
<div class="container-fluid py-4">
    <div class="card-module p-4 max-w-2xl mx-auto shadow-lg" style="max-width: 800px; margin: 0 auto; border-top: 5px solid var(--primary-royal);">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-white mb-0">
                <i class="fas fa-network-wired text-primary me-2"></i> Nueva Célula Familiar
            </h3>
            <a href="{{ route('celulas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('celulas.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label text-muted fw-bold small text-uppercase">Nombre de la Célula *</label>
                    <input type="text" name="nombre" class="form-control bg-dark border-secondary text-white" placeholder="Ej: Célula Emanuel, Sector Norte..." required>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted fw-bold small text-uppercase">Sector / Zona</label>
                    <input type="text" name="sector" class="form-control bg-dark border-secondary text-white" placeholder="Ej: Zona 1, Aldea...">
                </div>

                <div class="col-md-12">
                    <label class="form-label text-muted fw-bold small text-uppercase">Líder Responsable *</label>
                    <select name="lider_id" id="lider_select" class="form-select" required>
                        <option value="">Buscar por nombre o DPI...</option>
                        @foreach($miembros as $m)
                            <option value="{{ $m->id }}">#{{ $m->id }} - {{ $m->nombres }} {{ $m->apellidos }} (DPI: {{ $m->dpi }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-bold small text-uppercase">Día de Reunión *</label>
                    <select name="dia_reunion" class="form-select bg-dark border-secondary text-white" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted fw-bold small text-uppercase">Hora de Reunión *</label>
                    <input type="time" name="hora_reunion" class="form-control bg-dark border-secondary text-white" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label text-muted fw-bold small text-uppercase">Dirección Exacta</label>
                    <textarea name="direccion" class="form-control bg-dark border-secondary text-white" rows="2" placeholder="Describe la ubicación exacta para que el Pastor pueda visitarlos..."></textarea>
                </div>
            </div>

            <div class="mt-5 pt-3 border-top border-secondary border-opacity-10 d-grid">
                <button type="submit" class="btn btn-primary py-3 fw-bold fs-5 shadow">
                    <i class="fas fa-save me-2"></i> Registrar Célula
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#lider_select').select2({
            placeholder: "Buscar por nombre o DPI...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
