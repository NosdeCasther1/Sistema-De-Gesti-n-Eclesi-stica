@extends('layouts.app')

@section('title', 'Editar Célula Familiar - AD Rey de Reyes')

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

    /* Select2 custom styling for premium light/dark mode */
    .select2-container--default .select2-selection--single {
        background-color: var(--bg-card, #f8fafc) !important;
        border-color: var(--border-color, #cbd5e1) !important;
        height: auto !important;
        padding: 0.65rem 1rem !important;
        border-radius: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-color, #0f172a) !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        line-height: normal !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50% !important;
        transform: translateY(-50%) !important;
        right: 12px !important;
    }
    .select2-dropdown {
        background-color: var(--bg-body, #ffffff) !important;
        border-color: var(--border-color, #cbd5e1) !important;
        border-radius: 12px !important;
        padding: 8px !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-results__option {
        font-size: 0.75rem !important;
        padding: 8px 12px !important;
        color: var(--text-color, #0f172a) !important;
    }
    .select2-search__field {
        background-color: var(--bg-card, #f8fafc) !important;
        border-color: var(--border-color, #cbd5e1) !important;
        color: var(--text-color, #0f172a) !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        padding: 6px 10px !important;
    }
</style>
@endpush

@section('header_title', 'Editar Célula')
@section('header_subtitle', 'Modifique los datos y asignaciones de la célula seleccionada')
@section('header_icon')
<i class="fas fa-network-wired fs-5"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-5xl mx-auto">
    <!-- Barra de Navegación / Regreso -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1 flex items-center gap-3">
                <span>Editar Célula: {{ $celula->nombre }}</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Actualice la información de liderazgo, horarios y dirección de la célula</p>
        </div>
        <a href="{{ route('celulas.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-sm transition-all no-underline">
            <i class="fas fa-arrow-left text-sm"></i>
            <span>Volver al listado</span>
        </a>
    </div>

    <!-- Contenedor Principal Bento -->
    <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
        <!-- Glow de fondo -->
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

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

        <form action="{{ route('celulas.update', $celula->id) }}" method="POST" class="m-0 space-y-10">
            @csrf
            @method('PUT')

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
                        <input type="text" name="nombre" value="{{ old('nombre', $celula->nombre) }}" placeholder="Ej: Célula Emanuel, Célula Jerusalén" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Sector / Zona</label>
                        <input type="text" name="sector" value="{{ old('sector', $celula->sector) }}" placeholder="Ej: Sector Norte, Zona 1"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div class="md:col-span-12">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Líder Responsable *</label>
                        <select name="lider_id" id="lider_select" class="w-full" required>
                            <option value="">Buscar por nombre, apellidos o DPI...</option>
                            @foreach($miembros as $m)
                                <option value="{{ $m->id }}" {{ old('lider_id', $celula->lider_id) == $m->id ? 'selected' : '' }}>
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
                                <option value="{{ $dia }}" {{ old('dia_reunion', $celula->dia_reunion) == $dia ? 'selected' : '' }}>{{ $dia }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Hora de Reunión *</label>
                        <input type="time" name="hora_reunion" value="{{ old('hora_reunion', \Carbon\Carbon::parse($celula->hora_reunion)->format('H:i')) }}" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Dirección de Célula</label>
                        <textarea name="direccion" rows="2" placeholder="Describa la dirección exacta o coordenadas de la célula..."
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm resize-none">{{ old('direccion', $celula->direccion) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Botones de Guardado -->
            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('celulas.index') }}" class="px-6 py-3.5 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all no-underline cursor-pointer">Cancelar</a>
                <button type="submit" class="btn-bento-primary px-8 py-3.5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                    <i class="fas fa-save text-base"></i>
                    <span>Guardar Cambios</span>
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
            placeholder: "Buscar por nombre, apellidos o DPI...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
