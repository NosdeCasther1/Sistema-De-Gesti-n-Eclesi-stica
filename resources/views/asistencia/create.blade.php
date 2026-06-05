@extends('layouts.app')

@section('title', 'Registro Manual de Asistencia - AD Rey de Reyes')
@section('header_icon')<i class="fas fa-pen-to-square fs-5"></i>@endsection
@section('header_title', 'Registro Manual')
@section('header_subtitle', $contexto ? 'Registrando asistencia para: ' . ($contexto->nombre ?? $contexto->titulo) : 'Pase de lista general')

@push('styles')
<style>
    /* Action button - dark variant (guaranteed in both themes) */
    .btn-bento-dark {
        background-color: #1e293b;
        color: #ffffff;
        border: none;
        text-decoration: none;
    }
    .btn-bento-dark:hover {
        background-color: #334155;
        color: #ffffff;
        text-decoration: none;
        opacity: 0.9;
    }
    [data-theme='dark'] .btn-bento-dark {
        background-color: #334155;
    }
    [data-theme='dark'] .btn-bento-dark:hover {
        background-color: #475569;
    }
    /* Action button - ghost variant */
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

    .bento-select-safe {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 2.5rem !important;
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    .bento-select-safe option {
        background-color: var(--bg-card) !important;
        color: var(--text-primary) !important;
    }

    /* Select2 premium dark & light overrides using CSS variables */
    .select2-container--default .select2-selection--single {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 16px !important;
        height: 48px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 16px !important;
        transition: all 0.2s !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-main) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        padding-left: 0 !important;
        line-height: 1 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6b7280 transparent transparent transparent !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15) !important;
    }
    .select2-dropdown {
        background: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        margin-top: 4px !important;
    }
    .select2-container--default .select2-results__option {
        color: var(--text-secondary) !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        transition: background-color 0.15s ease, color 0.15s ease !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    [data-theme='dark'] .select2-container--default .select2-results__option--highlighted[aria-selected],
    [data-theme='dark'] .select2-container--default .select2-results__option--highlighted {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: rgba(37, 99, 235, 0.12) !important;
        color: #2563eb !important;
    }
    [data-theme='dark'] .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: rgba(59, 130, 246, 0.15) !important;
        color: #60a5fa !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"].select2-results__option--highlighted {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    [data-theme='dark'] .select2-container--default .select2-results__option[aria-selected="true"].select2-results__option--highlighted {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .select2-search--dropdown .select2-search__field {
        background: var(--bg-body) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 10px !important;
        color: var(--text-main) !important;
        padding: 8px 12px !important;
        font-size: 12px !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
    }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 space-y-5">

    {{-- Top Actions Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ url()->previous() }}" class="btn-bento-ghost inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold shadow-sm transition-all">
            <i class="fas fa-arrow-left text-xs"></i> Volver
        </a>
        <a href="{{ route('asistencia.scanner', ['celula_id' => $celula_id, 'evento_id' => $evento_id]) }}"
           class="btn-bento-dark inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold shadow-lg transition-all">
            <i class="fas fa-qrcode text-xs"></i> Usar Escáner QR
        </a>
    </div>

    {{-- Context Alert --}}
    @if($contexto)
    <div class="flex items-center gap-3 px-5 py-4 rounded-2xl" style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); color: #3b82f6;">
        <i class="fas fa-info-circle text-lg flex-shrink-0"></i>
        <p class="text-xs font-bold mb-0">
            Registrando asistencia para: <span style="color: #2563eb; font-weight: 900;">{{ $contexto->nombre ?? $contexto->titulo }}</span>
        </p>
    </div>
    @endif

    {{-- Main Form Card --}}
    <div style="background-color: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-3xl shadow-xl relative overflow-hidden">
        <!-- Background glow -->
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full blur-3xl pointer-events-none" style="background: rgba(245,158,11,0.08);"></div>

        {{-- Card Header --}}
        <div class="px-6 pt-6 pb-5 relative z-10" style="border-bottom: 1px solid var(--border-color);">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0" style="background: linear-gradient(135deg, #f59e0b, #ea580c); box-shadow: 0 8px 20px rgba(245,158,11,0.3);">
                    <i class="fas fa-pen-to-square text-white"></i>
                </div>
                <div>
                    <h2 class="text-base font-black tracking-tight mb-0" style="color: var(--text-primary);">Asistencia Manual</h2>
                    <p class="text-[10px] font-medium mb-0" style="color: var(--text-muted);">Busca al miembro y registra su presencia</p>
                </div>
            </div>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('asistencia.registrar') }}" method="POST" class="px-6 pt-6 pb-6 space-y-5 relative z-10 m-0">
            @csrf
            <input type="hidden" name="fecha" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="hora"  value="{{ date('H:i:s') }}">

            {{-- Destino --}}
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">Destino de la Asistencia</label>
                <select id="select_tipo_contexto_manual" onchange="toggleManualContext()"
                        class="bento-select-safe w-full rounded-2xl px-4 py-3 text-xs font-bold focus:outline-none transition-all shadow-sm">
                    <option value="general" {{ !$celula_id && !$evento_id ? 'selected' : '' }}>📋 Pase de Lista General</option>
                    <option value="evento"  {{ $evento_id  ? 'selected' : '' }}>🎙️ Asistencia a Evento / Culto</option>
                    <option value="celula"  {{ $celula_id  ? 'selected' : '' }}>🏠 Asistencia a Célula Familiar</option>
                </select>
            </div>

            {{-- Evento selector --}}
            <div id="div_select_evento_manual" style="{{ !$evento_id ? 'display: none;' : '' }}">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">Seleccionar Evento</label>
                <select name="evento_id" id="select_evento_id_manual" onchange="updateManualContextUrl('evento')"
                        class="bento-select-safe w-full rounded-2xl px-4 py-3 text-xs font-bold focus:outline-none transition-all shadow-sm">
                    <option value="">Seleccione un evento...</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->id }}" {{ $evento_id == $ev->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($ev->fecha_inicio)->translatedFormat('d M Y') }} — {{ $ev->titulo }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Célula selector --}}
            <div id="div_select_celula_manual" style="{{ !$celula_id ? 'display: none;' : '' }}">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">Seleccionar Célula</label>
                <select name="celula_id" id="select_celula_id_manual" onchange="updateManualContextUrl('celula')"
                        class="bento-select-safe w-full rounded-2xl px-4 py-3 text-xs font-bold focus:outline-none transition-all shadow-sm">
                    <option value="">Seleccione una célula...</option>
                    @foreach($celulas as $cel)
                        <option value="{{ $cel->id }}" {{ $celula_id == $cel->id ? 'selected' : '' }}>{{ $cel->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Divider --}}
            <div style="border-top: 1px solid var(--border-color);"></div>

            {{-- Member Search --}}
            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider mb-2" style="color: var(--text-muted);">
                    <i class="fas fa-search mr-1" style="color: #f59e0b;"></i> Seleccionar Miembro *
                </label>
                <select name="miembro_id" id="miembro_select" required>
                    <option value="">Buscar por nombre o DPI...</option>
                    @foreach($miembros as $m)
                        <option value="{{ $m->id }}">#{{ $m->id }} — {{ $m->nombres }} {{ $m->apellidos }} (DPI: {{ $m->dpi }})</option>
                    @endforeach
                </select>
                <p class="text-[11px] font-medium mt-2 mb-0" style="color: var(--text-muted);">
                    <i class="fas fa-keyboard mr-1"></i> Escribe el nombre o número de DPI para filtrar rápidamente.
                </p>
            </div>

            {{-- Submit --}}
            <div class="pt-2 space-y-3">
                <button type="submit"
                        class="w-full py-4 rounded-2xl font-black text-sm uppercase tracking-widest flex items-center justify-center gap-2.5 transition-all active:scale-95"
                        style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #ffffff; box-shadow: 0 8px 25px rgba(245,158,11,0.35);">
                    <i class="fas fa-check-circle text-base"></i> Registrar Asistencia
                </button>
                <a href="{{ url()->previous() }}" class="block w-full py-3 rounded-2xl text-center text-xs font-bold transition-all no-underline" style="color: var(--text-muted);">
                    Cancelar y volver
                </a>
            </div>
        </form>
    </div>

    {{-- Info Footer --}}
    <div class="flex items-center gap-3 px-5 py-4 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
        <i class="fas fa-clock text-lg flex-shrink-0" style="color: var(--text-muted);"></i>
        <div>
            <p class="text-[11px] font-bold mb-0" style="color: var(--text-secondary);">Fecha y hora del registro</p>
            <p class="text-[11px] font-mono mb-0" style="color: var(--text-muted);">{{ now()->translatedFormat('l, d \d\e F Y · H:i') }}</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleManualContext() {
        const tipo = document.getElementById('select_tipo_contexto_manual').value;
        const divEvento = document.getElementById('div_select_evento_manual');
        const divCelula = document.getElementById('div_select_celula_manual');
        const selEvento = document.getElementById('select_evento_id_manual');
        const selCelula = document.getElementById('select_celula_id_manual');

        if (tipo === 'general') {
            window.location.href = "{{ route('asistencia.manual') }}";
        } else if (tipo === 'evento') {
            divEvento.style.display = 'block';
            divCelula.style.display = 'none';
            selCelula.value = '';
        } else if (tipo === 'celula') {
            divCelula.style.display = 'block';
            divEvento.style.display = 'none';
            selEvento.value = '';
        }
    }

    function updateManualContextUrl(tipo) {
        const val = tipo === 'evento'
            ? document.getElementById('select_evento_id_manual').value
            : document.getElementById('select_celula_id_manual').value;
        if (val) {
            window.location.href = "{{ route('asistencia.manual') }}?" + tipo + "_id=" + val;
        } else {
            window.location.href = "{{ route('asistencia.manual') }}";
        }
    }

    $(document).ready(function () {
        $('#miembro_select').select2({
            placeholder: "Buscar por nombre o DPI...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('body')
        });
    });
</script>
@endpush
