@extends('layouts.app')

@section('title', 'Asistencia Manual - AD Rey de Reyes')

@section('content')
<div class="container-fluid py-4">
    <div class="card-module p-4 max-w-2xl mx-auto shadow-lg" style="max-width: 600px; margin: 0 auto; border-top: 5px solid var(--gold-royal);">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-white mb-0">
                <i class="fas fa-edit text-warning me-2"></i> Asistencia Manual
            </h3>
            <a href="{{ route('asistencia.scanner', ['celula_id' => $celula_id, 'evento_id' => $evento_id]) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-qrcode me-1"></i> Usar Escáner
            </a>
        </div>

        @if($contexto)
            <div class="alert alert-info bg-primary bg-opacity-10 border-0 text-primary mb-4">
                <i class="fas fa-info-circle me-2"></i> Registrando asistencia para: <strong>{{ $contexto->nombre ?? $contexto->titulo }}</strong>
            </div>
        @endif

        <form action="{{ route('asistencia.registrar') }}" method="POST">
            @csrf
            <input type="hidden" name="fecha" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="hora" value="{{ date('H:i:s') }}">

            <div class="mb-4">
                <label class="form-label text-muted fw-bold small text-uppercase mb-1">Destino de la Asistencia</label>
                <select id="select_tipo_contexto_manual" class="form-select bg-dark border-secondary text-white py-2 mb-3" onchange="toggleManualContext()">
                    <option value="general" {{ !$celula_id && !$evento_id ? 'selected' : '' }}>Pase de Lista General</option>
                    <option value="evento" {{ $evento_id ? 'selected' : '' }}>Asistencia a Evento / Culto</option>
                    <option value="celula" {{ $celula_id ? 'selected' : '' }}>Asistencia a Célula Familiar</option>
                </select>

                <div id="div_select_evento_manual" style="{{ !$evento_id ? 'display: none;' : '' }}">
                    <label class="form-label text-muted small fw-bold mb-1">Seleccionar Evento</label>
                    <select name="evento_id" id="select_evento_id_manual" class="form-select bg-dark border-secondary text-white py-2">
                        <option value="">Seleccione un evento...</option>
                        @foreach($eventos as $ev)
                            <option value="{{ $ev->id }}" {{ $evento_id == $ev->id ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($ev->fecha_inicio)->translatedFormat('d M Y') }} - {{ $ev->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="div_select_celula_manual" style="{{ !$celula_id ? 'display: none;' : '' }}">
                    <label class="form-label text-muted small fw-bold mb-1">Seleccionar Célula</label>
                    <select name="celula_id" id="select_celula_id_manual" class="form-select bg-dark border-secondary text-white py-2">
                        <option value="">Seleccione una célula...</option>
                        @foreach($celulas as $cel)
                            <option value="{{ $cel->id }}" {{ $celula_id == $cel->id ? 'selected' : '' }}>{{ $cel->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-bold small text-uppercase">Seleccionar Miembro *</label>
                <select name="miembro_id" id="miembro_select" class="form-select" required>
                    <option value="">Buscar por nombre o DPI...</option>
                    @foreach($miembros as $m)
                        <option value="{{ $m->id }}">#{{ $m->id }} - {{ $m->nombres }} {{ $m->apellidos }} (DPI: {{ $m->dpi }})</option>
                    @endforeach
                </select>
                <div class="smaller text-muted mt-2">Puedes buscar rápidamente escribiendo el nombre o DPI.</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-3 fw-bold shadow">
                    <i class="fas fa-check-circle me-2"></i> Registrar Asistencia
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-link text-muted">Cancelar y volver</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleManualContext() {
        const tipo = document.getElementById('select_tipo_contexto_manual').value;
        if (tipo === 'general') {
            document.getElementById('div_select_evento_manual').style.display = 'none';
            document.getElementById('div_select_celula_manual').style.display = 'none';
            document.getElementById('select_evento_id_manual').value = '';
            document.getElementById('select_celula_id_manual').value = '';
        } else if (tipo === 'evento') {
            document.getElementById('div_select_evento_manual').style.display = 'block';
            document.getElementById('div_select_celula_manual').style.display = 'none';
            document.getElementById('select_celula_id_manual').value = '';
        } else if (tipo === 'celula') {
            document.getElementById('div_select_celula_manual').style.display = 'block';
            document.getElementById('div_select_evento_manual').style.display = 'none';
            document.getElementById('select_evento_id_manual').value = '';
        }
    }

    $(document).ready(function() {
        $('#miembro_select').select2({
            placeholder: "Buscar por nombre o DPI...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
