@extends('layouts.app')

@section('title', 'Escáner de Asistencia QR - AD Rey de Reyes')
@section('header_icon')<i class="fas fa-qrcode fs-5"></i>@endsection
@section('header_title', 'Escáner QR')
@section('header_subtitle', $contexto ? 'Registrando para: ' . ($contexto->nombre ?? $contexto->titulo) : 'Pase de lista general')

@push('styles')
<style>
    /* Bento Premium - Scanner Module */
    .scanner-wrapper {
        background: #000;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
    }

    /* Scan overlay corners */
    .scan-corner {
        position: absolute;
        width: 28px;
        height: 28px;
        z-index: 10;
        pointer-events: none;
    }
    .scan-corner.tl { top: 18px; left: 18px; border-top: 3px solid #3b82f6; border-left: 3px solid #3b82f6; border-radius: 4px 0 0 0; }
    .scan-corner.tr { top: 18px; right: 18px; border-top: 3px solid #3b82f6; border-right: 3px solid #3b82f6; border-radius: 0 4px 0 0; }
    .scan-corner.bl { bottom: 18px; left: 18px; border-bottom: 3px solid #3b82f6; border-left: 3px solid #3b82f6; border-radius: 0 0 0 4px; }
    .scan-corner.br { bottom: 18px; right: 18px; border-bottom: 3px solid #3b82f6; border-right: 3px solid #3b82f6; border-radius: 0 0 4px 0; }

    @keyframes scanline {
        0%   { top: 18px; opacity: 1; }
        50%  { opacity: 0.6; }
        100% { top: calc(100% - 18px); opacity: 1; }
    }
    .scan-line {
        position: absolute;
        left: 18px; right: 18px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #3b82f6, #6366f1, #3b82f6, transparent);
        border-radius: 4px;
        animation: scanline 2.2s ease-in-out infinite alternate;
        z-index: 9;
        box-shadow: 0 0 8px #3b82f6, 0 0 20px rgba(59,130,246,0.4);
    }

    /* Result feedback card */
    .result-card {
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.4s ease;
    }
    .result-card.success {
        background: rgba(16, 185, 129, 0.08);
        border-color: rgba(16, 185, 129, 0.3);
    }
    .result-card.error {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.3);
    }

    /* Context selector - premium selects */
    .bento-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 2.5rem !important;
    }

    /* Pulse animation for scanning state */
    @keyframes pulse-ring {
        0%   { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5); }
        70%  { transform: scale(1);   box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .scanning-badge {
        animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
    }

    /* --- Safe Action Buttons (CSS-based, no Tailwind dependency) --- */
    .btn-bento-ghost {
        background-color: #ffffff;
        color: #334155;
        border: 1px solid #e2e8f0;
        text-decoration: none;
    }
    .btn-bento-ghost:hover {
        background-color: #f8fafc;
        color: #1e293b;
        text-decoration: none;
    }
    [data-theme='dark'] .btn-bento-ghost {
        background-color: #1e293b;
        color: #cbd5e1;
        border-color: #334155;
    }
    [data-theme='dark'] .btn-bento-ghost:hover {
        background-color: #334155;
    }
    .btn-bento-amber {
        background-color: #f59e0b;
        color: #ffffff;
        border: none;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }
    .btn-bento-amber:hover {
        background-color: #d97706;
        color: #ffffff;
        text-decoration: none;
    }

    /* --- Bento Select fix for light mode --- */
    .bento-select-safe {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 2.5rem !important;
        background-color: #f8fafc;
        color: #0f172a;
        border: 1px solid #e2e8f0;
    }
    [data-theme='dark'] .bento-select-safe {
        background-color: #1e293b;
        color: #ffffff;
        border-color: rgba(51, 65, 85, 0.8);
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 space-y-5">

    {{-- Top Actions Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ url()->previous() }}" class="btn-bento-ghost inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold shadow-sm transition-all">
            <i class="fas fa-arrow-left text-xs"></i> Volver
        </a>
        <a href="{{ route('asistencia.manual', ['celula_id' => $celula_id, 'evento_id' => $evento_id]) }}"
           class="btn-bento-amber inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition-all">
            <i class="fas fa-pen-to-square text-xs"></i> Registro Manual
        </a>
    </div>

    {{-- Context Configuration Card --}}
    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <!-- Glow -->
        <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full blur-3xl pointer-events-none" style="background: rgba(59,130,246,0.08);"></div>
        
        <div class="flex items-center gap-3 mb-5 relative z-10">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0" style="background: linear-gradient(135deg, #3b82f6, #6366f1); box-shadow: 0 8px 20px rgba(59,130,246,0.3);">
                <i class="fas fa-sliders-h text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-black tracking-tight mb-0" style="color: var(--text-primary);">Configuración de Asistencia</h2>
                <p class="text-[10px] font-medium mb-0" style="color: var(--text-muted);">Selecciona el destino del registro</p>
            </div>
        </div>

        <div class="space-y-4 relative z-10">
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">Tipo de Registro</label>
                <select id="select_tipo_contexto" onchange="toggleContextFields()"
                        class="bento-select-safe w-full rounded-2xl px-4 py-3 text-xs font-bold focus:outline-none transition-all shadow-sm">
                    <option value="general" {{ !$celula_id && !$evento_id ? 'selected' : '' }}>📋 Pase de Lista General</option>
                    <option value="evento" {{ $evento_id ? 'selected' : '' }}>🎙️ Asistencia a Evento / Culto</option>
                    <option value="celula" {{ $celula_id ? 'selected' : '' }}>🏠 Asistencia a Célula Familiar</option>
                </select>
            </div>

            <div id="div_select_evento" style="{{ !$evento_id ? 'display: none;' : '' }}">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">Seleccionar Evento</label>
                <select id="select_evento_id" onchange="updateContextUrl('evento')"
                        class="bento-select-safe w-full rounded-2xl px-4 py-3 text-xs font-bold focus:outline-none transition-all shadow-sm">
                    <option value="">Seleccione un evento...</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->id }}" {{ $evento_id == $ev->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($ev->fecha_inicio)->translatedFormat('d M Y') }} — {{ $ev->titulo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="div_select_celula" style="{{ !$celula_id ? 'display: none;' : '' }}">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">Seleccionar Célula</label>
                <select id="select_celula_id" onchange="updateContextUrl('celula')"
                        class="bento-select-safe w-full rounded-2xl px-4 py-3 text-xs font-bold focus:outline-none transition-all shadow-sm">
                    <option value="">Seleccione una célula...</option>
                    @foreach($celulas as $cel)
                        <option value="{{ $cel->id }}" {{ $celula_id == $cel->id ? 'selected' : '' }}>
                            {{ $cel->nombre }} — Líder: {{ $cel->lider->nombres ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Scanner Card --}}
    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-3xl overflow-hidden shadow-xl">
        {{-- Scanner Header --}}
        <div class="px-6 pt-6 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <i class="fas fa-qrcode text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black tracking-tight mb-0" style="color: var(--text-primary);">Escáner de Carnets</h2>
                    <p class="text-[10px] font-medium mb-0" style="color: var(--text-muted);">Apunta la cámara al QR del carnet</p>
                </div>
            </div>
            <div class="scanning-badge inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2);">
                <span class="w-2 h-2 rounded-full" style="background: #3b82f6; display: inline-block;"></span> Activo
            </div>
        </div>

        {{-- Camera Viewport --}}
        <div class="mx-6 mb-4 scanner-wrapper" style="min-height: 260px;">
            <div class="scan-corner tl"></div>
            <div class="scan-corner tr"></div>
            <div class="scan-corner bl"></div>
            <div class="scan-corner br"></div>
            <div class="scan-line" id="scan-line"></div>
            <div id="reader" style="width: 100%; border: none;"></div>
        </div>

        {{-- Result Container --}}
        <div id="result-container" class="mx-6 mb-4 hidden">
            <div id="result-card-inner" class="result-card">
                <div id="result-icon" class="text-4xl mb-3"></div>
                <h3 id="result-title" class="text-base font-black mb-1"></h3>
                <p id="result-message" class="text-sm font-medium opacity-80 mb-0"></p>
            </div>
        </div>

        {{-- Restart Button --}}
        <div id="btn-restart-wrapper" class="px-6 pb-6 hidden">
            <button id="btn-restart"
                    class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                <i class="fas fa-redo"></i> Escanear Siguiente
            </button>
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
        const val = tipo === 'evento'
            ? document.getElementById('select_evento_id').value
            : document.getElementById('select_celula_id').value;
        if (val) {
            window.location.href = "{{ route('asistencia.scanner') }}?" + tipo + "_id=" + val;
        }
    }

    const html5QrCode = new Html5Qrcode("reader");
    const resultContainer   = document.getElementById('result-container');
    const resultCard        = document.getElementById('result-card-inner');
    const resultTitle       = document.getElementById('result-title');
    const resultMessage     = document.getElementById('result-message');
    const resultIcon        = document.getElementById('result-icon');
    const btnRestartWrapper = document.getElementById('btn-restart-wrapper');
    const btnRestart        = document.getElementById('btn-restart');
    const audioSuccess      = document.getElementById('audio-success');
    const audioError        = document.getElementById('audio-error');

    const qrConfig = { fps: 10, qrbox: { width: 240, height: 240 } };

    function onScanSuccess(decodedText) {
        html5QrCode.stop().then(() => {
            // Hide the scan line animation while not scanning
            document.getElementById('scan-line').style.display = 'none';
            processAttendance(decodedText);
        });
    }

    function processAttendance(qrData) {
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

        const currentEventoId = document.getElementById('select_evento_id')
            ? document.getElementById('select_evento_id').value
            : "{{ $evento_id }}";
        const currentCelulaId = document.getElementById('select_celula_id')
            ? document.getElementById('select_celula_id').value
            : "{{ $celula_id }}";

        fetch("{{ route('asistencia.registrar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                miembro_id: miembroId,
                celula_id:  currentCelulaId,
                evento_id:  currentEventoId,
                fecha: "{{ date('Y-m-d') }}",
                hora:  "{{ date('H:i:s') }}"
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                audioSuccess.play();
                showResult(true, 'Asistencia Registrada', data.miembro);
            } else {
                audioError.play();
                showResult(false, 'Aviso', data.message);
            }
        })
        .catch(() => {
            audioError.play();
            showResult(false, 'Error de Conexión', 'No se pudo contactar con el servidor.');
        });
    }

    function showResult(success, title, message) {
        resultCard.className = 'result-card ' + (success ? 'success' : 'error');
        resultIcon.innerHTML = success
            ? '<i class="fas fa-check-circle text-emerald-500"></i>'
            : '<i class="fas fa-exclamation-circle text-rose-500"></i>';
        resultTitle.innerText  = title;
        resultTitle.className  = 'text-base font-black mb-1 ' + (success ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400');
        resultMessage.innerText = message;
        resultMessage.className = 'text-sm font-medium mb-0 ' + (success ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400');
        resultContainer.classList.remove('hidden');
        btnRestartWrapper.classList.remove('hidden');
    }

    btnRestart.addEventListener('click', () => {
        resultContainer.classList.add('hidden');
        btnRestartWrapper.classList.add('hidden');
        document.getElementById('scan-line').style.display = 'block';
        startScanner();
    });

    function startScanner() {
        html5QrCode.start({ facingMode: "environment" }, qrConfig, onScanSuccess);
    }

    startScanner();
</script>
@endpush
