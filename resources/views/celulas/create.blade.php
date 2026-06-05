@extends('layouts.app')

@section('title', 'Nueva Célula Familiar - AD Rey de Reyes')

@push('styles')
<style>
    /* Bento Card Hover Effects */
    .bento-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bento-card:hover {
        transform: translateY(-4px);
    }

    /* Bento Buttons Premium */
    .btn-bento-primary {
        background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(37,99,235,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
        box-shadow: 0 6px 20px rgba(37,99,235,0.35) !important;
        transform: translateY(-2px);
    }

    /* Icon Boxes Premium */
    .stat-icon-box {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        min-height: 46px !important;
        border-radius: 14px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    .stat-icon-box i {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-block !important;
        font-size: 1.25rem !important;
    }

    /* Select2 custom styling for premium light/dark mode using CSS variables */
    .select2-container--default .select2-selection--single {
        background-color: rgba(var(--bg-card-rgb), 0.5) !important;
        border: 1px solid var(--border-color) !important;
        height: 46px !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 1rem !important;
        padding-right: 2.5rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-main) !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        line-height: normal !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .select2-dropdown {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 12px !important;
        padding: 8px !important;
        box-shadow: var(--shadow-md) !important;
        z-index: 9999 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-container--default .select2-results__option--highlighted {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-radius: 8px !important;
    }
    [data-theme='dark'] .select2-container--default .select2-results__option--highlighted[aria-selected],
    [data-theme='dark'] .select2-container--default .select2-results__option--highlighted {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .select2-container--default .select2-results__option {
        font-size: 0.75rem !important;
        padding: 8px 12px !important;
        color: var(--text-secondary) !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: rgba(37, 99, 235, 0.12) !important;
        color: #2563eb !important;
        border-radius: 8px !important;
    }
    [data-theme='dark'] .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: rgba(59, 130, 246, 0.15) !important;
        color: #60a5fa !important;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"].select2-results__option--highlighted {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    [data-theme='dark'] .select2-container--default .select2-results__option[aria-selected="true"].select2-results__option--highlighted {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    .select2-search__field {
        background-color: var(--bg-body) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        padding: 6px 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--text-muted) !important;
    }
</style>
@endpush

@section('header_title', 'Registro de Célula')
@section('header_subtitle', 'Organización territorial y multiplicación ministerial')
@section('header_icon')
<i class="fas fa-network-wired fs-5"></i>
@endsection

@section('content')
{{-- FONDO MAESTRO DE LA PANTALLA --}}
<div class="min-h-screen bg-slate-50 dark:bg-[#0B1121] p-4 lg:p-8 font-sans antialiased -m-4">
    {{-- CONTENEDOR CENTRALIZADO --}}
    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- HEADER DEL FORMULARIO --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Registrar Nueva Célula</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Defina el nombre, líder a cargo e información de la célula.</p>
            </div>
            <a href="{{ route('celulas.index') }}" class="shrink-0 flex items-center justify-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver al listado
            </a>
        </div>

        {{-- TARJETA DEL FORMULARIO --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm">
            @if($errors->any())
            <div class="mb-8 p-5 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 shadow-sm">
                <div class="font-bold text-xs mb-2 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Por favor corrige los siguientes errores:</span>
                </div>
                <ul class="mb-0 ps-5 text-xs space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('celulas.store') }}" method="POST" class="m-0 space-y-10">
                @csrf

                {{-- SECCIÓN 1: IDENTIFICACIÓN Y LIDERAZGO --}}
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="stat-icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 shadow-sm flex-shrink-0">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div>
                            <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">1. Identificación y Liderazgo</h5>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Nombre identificador y asignación del líder responsable</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-8">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre de la Célula *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Célula Emanuel, Célula Jerusalén" required
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Sector / Zona</label>
                            <input type="text" name="sector" value="{{ old('sector') }}" placeholder="Ej: Sector Norte, Zona 1"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                        </div>
                        <div class="md:col-span-12">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Líder Responsable *</label>
                            <select name="lider_id" id="lider_select" class="w-full" required>
                                <option value="">Buscar por nombre, apellidos o DPI...</option>
                                @foreach($miembros as $m)
                                    <option value="{{ $m->id }}" {{ old('lider_id') == $m->id ? 'selected' : '' }}>
                                        #{{ $m->id }} - {{ $m->nombres }} {{ $m->apellidos }} (DPI: {{ $m->dpi ?? 'Sin DPI' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: PLANIFICACIÓN Y UBICACIÓN --}}
                <div>
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="stat-icon-box bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-sm flex-shrink-0">
                            <i class="far fa-clock"></i>
                        </div>
                        <div>
                            <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">2. Reunión y Ubicación</h5>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Días, horarios y ubicación física del punto de encuentro</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Día de Reunión *</label>
                            <select name="dia_reunion" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                    <option value="{{ $dia }}" {{ old('dia_reunion') == $dia ? 'selected' : '' }}>{{ $dia }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Hora de Reunión *</label>
                            <input type="time" name="hora_reunion" value="{{ old('hora_reunion') }}" required
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Dirección de Célula</label>
                            <textarea name="direccion" rows="2" placeholder="Describa la dirección exacta o coordenadas de la célula..."
                                      class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm resize-none">{{ old('direccion') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de Guardado -->
                <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('celulas.index') }}" class="px-6 py-3.5 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all no-underline cursor-pointer">Cancelar</a>
                    <button type="submit" class="btn-bento-primary px-8 py-3.5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                        <i class="fas fa-save text-base"></i>
                        <span>Registrar Célula</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#lider_select').select2({
            placeholder: "Buscar por nombre, apellidos o DPI...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
