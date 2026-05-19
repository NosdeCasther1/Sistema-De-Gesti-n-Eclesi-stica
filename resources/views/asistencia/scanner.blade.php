@extends('layouts.app')

@section('title', 'Escáner de Asistencia - AD Rey de Reyes')

@section('header_title', 'Escáner de Asistencia')
@section('header_subtitle', $contexto ? 'Registrando para: ' . ($contexto->nombre ?? $contexto->titulo) : 'Pase de lista general')
@section('header_icon')
<i class="fas fa-qrcode fs-5"></i>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('asistencia.manual', ['celula_id' => $celula_id, 'evento_id' => $evento_id]) }}" class="btn btn-outline-warning px-4 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-edit"></i> <span>Registro Manual</span>
        </a>
    </div>

    <!-- TARJETA DE SELECCIÓN DE CONTEXTO -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="card-module p-4 shadow-sm border-top border-warning border-4">
                <h5 class="fw-bold text-white mb-3"><i class="fas fa-sliders-h text-warning me-2"></i> Configuración de Asistencia</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted small fw-bold mb-1">Tipo de Registro</label>
                        <select id="select_tipo_contexto" class="form-select bg-dark border-secondary text-white py-2" onchange="toggleContextFields()">
                            <option value="general" {{ !$celula_id && !$evento_id ? 'selected' : '' }}>Pase de Lista General</option>
                            <option value="evento" {{ $evento_id ? 'selected' : '' }}>Asistencia a Evento / Culto</option>
                            <option value="celula" {{ $celula_id ? 'selected' : '' }}>Asistencia a Célula Familiar</option>
                        </select>
                    </div>
                    
                    <div class="col-12" id="div_select_evento" style="{{ !$evento_id ? 'display: none;' : '' }}">
                        <label class="form-label text-muted small fw-bold mb-1">Seleccionar Evento</label>
                        <select id="select_evento_id" class="form-select bg-dark border-secondary text-white py-2" onchange="updateContextUrl('evento')">
                            <option value="">Seleccione un evento...</option>
                            @foreach($eventos as $ev)
                                <option value="{{ $ev->id }}" {{ $evento_id == $ev->id ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($ev->fecha_inicio)->translatedFormat('d M Y') }} - {{ $ev->titulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12" id="div_select_celula" style="{{ !$celula_id ? 'display: none;' : '' }}">
                        <label class="form-label text-muted small fw-bold mb-1">Seleccionar Célula</label>
                        <select id="select_celula_id" class="form-select bg-dark border-secondary text-white py-2" onchange="updateContextUrl('celula')">
                            <option value="">Seleccione una célula...</option>
                            @foreach($celulas as $cel)
                                <option value="{{ $cel->id }}" {{ $celula_id == $cel->id ? 'selected' : '' }}>{{ $cel->nombre }} (Líder: {{ $cel->lider->nombres ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-module p-3 shadow-lg bg-black">
                <!-- Zona del Escáner -->
                <div id="reader" style="width: 100%; border-radius: 15px; overflow: hidden; border: none;"></div>
                
                <div id="result-container" class="mt-4 p-3 rounded-3 d-none text-center">
                    <div id="result-icon" class="mb-2 fs-1"></div>
                    <h4 id="result-title" class="fw-bold mb-1"></h4>
                    <p id="result-message" class="mb-0"></p>
                </div>

                <div class="mt-3 text-center">
                    <button id="btn-restart" class="btn btn-outline-primary d-none">
                        <i class="fas fa-redo me-2"></i> Escanear Siguiente
                    </button>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ url()->previous() }}" class="btn btn-link text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Audio para feedback -->
<audio id="audio-success" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3"></audio>
<audio id="audio-error" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3"></audio>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function toggleContextFields() {
        const tipo = document.getElementById('select_tipo_contexto').value;
        if (tipo === 'general') {
            window.location.href = "{{ route('asistencia.scanner') }}";
        } else if (tipo === 'evento') {
            document.getElementById('div_select_evento').style.display = 'block';
            document.getElementById('div_select_celula').style.display = 'none';
        } else if (tipo === 'celula') {
            document.getElementById('div_select_celula').style.display = 'block';
            document.getElementById('div_select_evento').style.display = 'none';
        }
    }

    function updateContextUrl(tipo) {
        const val = tipo === 'evento' ? document.getElementById('select_evento_id').value : document.getElementById('select_celula_id').value;
        if (val) {
            window.location.href = "{{ route('asistencia.scanner') }}?" + tipo + "_id=" + val;
        }
    }

    const html5QrCode = new Html5Qrcode("reader");
    const resultContainer = document.getElementById('result-container');
    const resultTitle = document.getElementById('result-title');
    const resultMessage = document.getElementById('result-message');
    const resultIcon = document.getElementById('result-icon');
    const btnRestart = document.getElementById('btn-restart');
    const audioSuccess = document.getElementById('audio-success');
    const audioError = document.getElementById('audio-error');

    const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };

    function onScanSuccess(decodedText, decodedResult) {
        // Detener el escáner para procesar
        html5QrCode.stop().then(() => {
            processAttendance(decodedText);
        });
    }

    function processAttendance(qrData) {
        // Intentar extraer el ID del miembro del QR
        let miembroId = null;
        try {
            const urlParts = qrData.split('/');
            miembroId = urlParts[urlParts.length - 1];
        } catch (e) {
            showResult(false, 'Código Inválido', 'El QR no pertenece a un carnet válido.');
            return;
        }

        if (!miembroId || isNaN(miembroId)) {
            showResult(false, 'Código Inválido', 'El QR no contiene un ID de miembro válido.');
            return;
        }

        const currentEventoId = document.getElementById('select_evento_id') ? document.getElementById('select_evento_id').value : "{{ $evento_id }}";
        const currentCelulaId = document.getElementById('select_celula_id') ? document.getElementById('select_celula_id').value : "{{ $celula_id }}";

        // Enviar al servidor
        fetch("{{ route('asistencia.registrar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                miembro_id: miembroId,
                celula_id: currentCelulaId,
                evento_id: currentEventoId,
                fecha: "{{ date('Y-m-d') }}",
                hora: "{{ date('H:i:s') }}"
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                audioSuccess.play();
                showResult(true, 'Asistencia Exitosa', data.miembro);
            } else {
                audioError.play();
                showResult(false, 'Aviso', data.message);
            }
        })
        .catch(error => {
            audioError.play();
            showResult(false, 'Error de Conexión', 'No se pudo contactar con el servidor.');
        });
    }

    function showResult(success, title, message) {
        resultContainer.className = `mt-4 p-3 rounded-3 text-center ${success ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'}`;
        resultIcon.innerHTML = success ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
        resultTitle.innerText = title;
        resultMessage.innerText = message;
        resultContainer.classList.remove('d-none');
        btnRestart.classList.remove('d-none');
    }

    btnRestart.addEventListener('click', () => {
        resultContainer.classList.add('d-none');
        btnRestart.classList.add('d-none');
        startScanner();
    });

    function startScanner() {
        html5QrCode.start({ facingMode: "environment" }, qrConfig, onScanSuccess);
    }

    // Iniciar
    startScanner();
</script>
@endpush
