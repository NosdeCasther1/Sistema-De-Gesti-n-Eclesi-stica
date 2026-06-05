@extends('layouts.app')

@section('title', 'Editar Miembro: ' . $miembro->nombres . ' ' . $miembro->apellidos . ' - AD Rey de Reyes')

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
    .select2-container .select2-results__option--highlighted:not([aria-selected="true"]):not([aria-selected=true]):not(.select2-results__option--selected) {
        background-color: #f1f5f9 !important; /* slate-100 */
        color: #1e293b !important; /* slate-800 */
        border-radius: 8px !important;
    }
    .dark .select2-container .select2-results__option--highlighted:not([aria-selected="true"]):not([aria-selected=true]):not(.select2-results__option--selected),
    [data-theme='dark'] .select2-container .select2-results__option--highlighted:not([aria-selected="true"]):not([aria-selected=true]):not(.select2-results__option--selected) {
        background-color: #334155 !important; /* slate-700 */
        color: #ffffff !important;
    }
    .select2-container .select2-results__option--highlighted[aria-selected="true"],
    .select2-container .select2-results__option--highlighted[aria-selected=true],
    .select2-container .select2-results__option--highlighted.select2-results__option--selected {
        background-color: #2563eb !important; /* blue-600 */
        color: #ffffff !important;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-results__option {
        font-size: 0.75rem !important;
        padding: 10px 14px !important;
        margin-bottom: 2px !important;
        color: var(--text-secondary) !important;
        transition: all 0.2s ease !important;
    }
    .select2-container .select2-results__option[aria-selected="true"],
    .select2-container .select2-results__option[aria-selected=true],
    .select2-container .select2-results__option--selected {
        background-color: #1e40af !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 8px !important;
    }
    .select2-search__field {
        background-color: var(--bg-body) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        padding: 8px 12px !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        transition: all 0.2s ease !important;
    }
    .select2-search__field:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--text-muted) !important;
    }

    /* Simetría: etiquetas e inputs del bloque ministerial */
    .member-form-label {
        min-height: 2.75rem;
        display: flex;
        align-items: flex-end;
        line-height: 1.25;
    }
    .member-form-field input[type="date"],
    .member-form-field input[type="text"] {
        min-height: 2.75rem;
    }
    .member-baptism-card {
        min-height: 3.5rem;
    }
</style>
@endpush

@section('header_title', 'Edición de Miembro')
@section('header_subtitle', 'Actualización de expediente y datos de contacto')
@section('header_icon')
<i class="fas fa-user-edit fs-5"></i>
@endsection

@section('content')
<div class="py-8 px-4 max-w-5xl mx-auto">
    <!-- Barra de Navegación / Regreso -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1 flex items-center gap-3">
                <span>Editar Miembro: {{ $miembro->nombres }} {{ $miembro->apellidos }}</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Modifique los campos necesarios para mantener el padrón actualizado</p>
        </div>
        <a href="{{ route('miembros.show', $miembro->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-sm transition-all no-underline">
            <i class="fas fa-arrow-left text-sm"></i>
            <span>Volver al Perfil</span>
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

        <form action="{{ route('miembros.update', $miembro->id) }}" method="POST" enctype="multipart/form-data" class="m-0 space-y-10">
            @csrf
            @method('PUT')

            {{-- SECCIÓN 1: DATOS PERSONALES --}}
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="stat-icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 shadow-sm flex-shrink-0">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">1. Datos Personales</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Información de identidad y contacto directo</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombres *</label>
                        <input type="text" name="nombres" value="{{ old('nombres', $miembro->nombres) }}" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $miembro->apellidos) }}" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">DPI / Documento de Identidad *</label>
                        <input type="text" name="dpi" value="{{ old('dpi', $miembro->dpi) }}" required
                               maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 13)"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $miembro->fecha_nacimiento ? $miembro->fecha_nacimiento->format('Y-m-d') : '') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Sexo</label>
                        <select name="sexo" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <option value="">Seleccionar Sexo</option>
                            <option value="M" {{ old('sexo', $miembro->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $miembro->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Estado Civil</label>
                        <select name="estado_civil" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <option value="">Seleccionar Estado Civil</option>
                            <option value="Soltero(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Soltero(a)' ? 'selected' : '' }}>Soltero(a)</option>
                            <option value="Casado(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                            <option value="Divorciado(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                            <option value="Viudo(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Viudo(a)' ? 'selected' : '' }}>Viudo(a)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $miembro->telefono) }}" placeholder="Ej: 55551234 (8 dígitos)"
                               maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 8)"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $miembro->email) }}" placeholder="correo@ejemplo.com"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>

                    <div class="md:col-span-2 mt-4 pt-5 border-t border-slate-100 dark:border-slate-800" x-data="{}">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div class="stat-icon-box bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 shadow-sm flex-shrink-0">
                                <i class="fas fa-people-roof"></i>
                            </div>
                            <div>
                                <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">Núcleo Familiar y Domicilio</h5>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Relación familiar, cónyuge y dirección residencial</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Familia (Núcleo Familiar)</label>

                                <select name="familia_id" id="familia_select" x-on:change="let opt = $event.target.options[$event.target.selectedIndex]; document.querySelector('input[name=direccion]').value = opt.dataset.direccion || ''; document.querySelector('input[name=zona]').value = opt.dataset.zona || ''; document.querySelector('input[name=municipio]').value = opt.dataset.municipio || ''; document.querySelector('input[name=departamento]').value = opt.dataset.departamento || '';" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm cursor-pointer">
                                    <option value="" data-direccion="" data-zona="" data-municipio="" data-departamento="">Seleccionar Familia (Opcional)</option>
                                    @foreach(\App\Models\Familia::orderBy('nombre')->get() as $f)
                                        <option value="{{ $f->id }}" data-direccion="{{ $f->direccion }}" data-zona="{{ $f->zona }}" data-municipio="{{ $f->municipio }}" data-departamento="{{ $f->departamento }}" {{ old('familia_id', $miembro->familia_id) == $f->id ? 'selected' : '' }}>
                                            {{ $f->nombre }} {{ $f->direccion ? '- Dir: ' . $f->direccion : '' }} {{ $f->municipio ? '- ' . $f->municipio : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1.5 ml-1">Al seleccionar, la dirección se autocompletará.</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Cónyuge (Si aplica)</label>

                                <select name="conyuge_id" id="conyuge_select" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                                    <option value="">Seleccionar Cónyuge (Opcional)</option>
                                    @foreach($posiblesConyuges as $pc)
                                        <option value="{{ $pc->id }}" {{ old('conyuge_id', $miembro->conyuge_id) == $pc->id ? 'selected' : '' }}>
                                            {{ $pc->nombre_completo }} {{ $pc->dpi ? '(DPI: ' . $pc->dpi . ')' : '' }} {{ $pc->telefono ? '- Tel: ' . $pc->telefono : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Dirección Exacta (Calle, Avenida, Lote, Manzana)</label>
                                <input type="text" name="direccion" value="{{ old('direccion', $miembro->direccion) }}" placeholder="Ej: 4ta Calle 5-20"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Zona</label>
                                <input type="text" name="zona" value="{{ old('zona', $miembro->zona) }}" placeholder="Ej: Zona 1"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Municipio</label>
                                <input type="text" name="municipio" value="{{ old('municipio', $miembro->municipio) }}" placeholder="Ej: Mixco"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Departamento</label>
                                <input type="text" name="departamento" value="{{ old('departamento', $miembro->departamento) }}" placeholder="Ej: Guatemala"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: INFORMACIÓN ACADÉMICA Y LABORAL --}}
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="stat-icon-box bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 shadow-sm flex-shrink-0">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">2. Información Académica y Laboral</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Nivel de estudios y ocupación principal</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nivel Académico</label>
                        <select name="nivel_academico" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm cursor-pointer">
                            <option value="">Seleccionar Nivel</option>
                            <option value="Primaria" {{ old('nivel_academico', $miembro->nivel_academico) == 'Primaria' ? 'selected' : '' }}>Primaria</option>
                            <option value="Básicos" {{ old('nivel_academico', $miembro->nivel_academico) == 'Básicos' ? 'selected' : '' }}>Básicos</option>
                            <option value="Diversificado" {{ old('nivel_academico', $miembro->nivel_academico) == 'Diversificado' ? 'selected' : '' }}>Diversificado</option>
                            <option value="Universitario" {{ old('nivel_academico', $miembro->nivel_academico) == 'Universitario' ? 'selected' : '' }}>Universitario</option>
                            <option value="Maestría / Postgrado" {{ old('nivel_academico', $miembro->nivel_academico) == 'Maestría / Postgrado' ? 'selected' : '' }}>Maestría / Postgrado</option>
                            <option value="Ninguno" {{ old('nivel_academico', $miembro->nivel_academico) == 'Ninguno' ? 'selected' : '' }}>Ninguno</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Profesión / Oficio</label>
                        <input type="text" name="profesion" value="{{ old('profesion', $miembro->profesion) }}" placeholder="Ej: Perito Contador, Maestra..."
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Lugar de Trabajo o Estudio</label>
                        <input type="text" name="lugar_trabajo_estudio" value="{{ old('lugar_trabajo_estudio', $miembro->lugar_trabajo_estudio) }}" placeholder="Empresa o Institución"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: INFORMACIÓN MINISTERIAL --}}
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="stat-icon-box bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 shadow-sm flex-shrink-0">
                        <i class="fas fa-church"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">3. Información Ministerial</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Asignación de ministerio, consolidación y núcleo familiar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    {{-- ESTADO DEL MIEMBRO --}}
                    <div class="md:col-span-2 mb-6" x-data="{ activo: {{ $miembro->estado ? 'true' : 'false' }} }">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Estado del Miembro</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label @click="activo = true"
                                   :class="activo ? 'bg-emerald-50 dark:bg-emerald-500/15 border-emerald-500 text-emerald-700 dark:text-emerald-300 shadow-md shadow-emerald-500/10' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-400'"
                                   class="cursor-pointer border-2 rounded-2xl p-4 flex items-center gap-3 transition-all duration-200">
                                <input type="radio" name="estado" value="1" x-bind:checked="activo" class="sr-only">
                                <div :class="activo ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'" class="w-5 h-5 rounded-full flex items-center justify-center transition-colors shrink-0">
                                    <i class="fas fa-check text-white text-[9px]"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black leading-tight">Miembro Activo</p>
                                    <p class="text-[10px] font-medium opacity-70 leading-tight">Puede votar y participar</p>
                                </div>
                            </label>
                            <label @click="activo = false"
                                   :class="!activo ? 'bg-rose-50 dark:bg-rose-500/15 border-rose-500 text-rose-700 dark:text-rose-300 shadow-md shadow-rose-500/10' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-400'"
                                   class="cursor-pointer border-2 rounded-2xl p-4 flex items-center gap-3 transition-all duration-200">
                                <input type="radio" name="estado" value="0" x-bind:checked="!activo" class="sr-only">
                                <div :class="!activo ? 'bg-rose-500' : 'bg-slate-300 dark:bg-slate-600'" class="w-5 h-5 rounded-full flex items-center justify-center transition-colors shrink-0">
                                    <i class="fas fa-times text-white text-[9px]"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black leading-tight">Miembro Inactivo</p>
                                    <p class="text-[10px] font-medium opacity-70 leading-tight">Sin privilegios de voto</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ETAPA DE CONSOLIDACIÓN ESPIRITUAL --}}
                    <div class="md:col-span-2 mb-6">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Etapa de Consolidación Espiritual *</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3" x-data="{ etapa: '{{ old('etapa_consolidacion', $miembro->etapa_consolidacion) }}' }">

                            @php
                            $etapas = [
                                ['value' => 'Nuevo',            'icon' => 'fa-star',        'color' => 'slate',   'label' => 'Nuevo'],
                                ['value' => 'En Discipulado',   'icon' => 'fa-book-open',   'color' => 'blue',    'label' => 'En Discipulado'],
                                ['value' => 'Asignado a Célula','icon' => 'fa-users',        'color' => 'purple',  'label' => 'Asignado a Célula'],
                                ['value' => 'Bautizado',        'icon' => 'fa-dove',        'color' => 'emerald', 'label' => 'Bautizado ✓'],
                            ];
                            @endphp

                            @foreach($etapas as $e)
                            <label @click="etapa = '{{ $e['value'] }}'"
                                   :class="etapa === '{{ $e['value'] }}'
                                       ? '{{ $e['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900 border-emerald-500 text-emerald-700 dark:text-emerald-300 shadow-md' : ($e['color'] === 'blue' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500 text-blue-700 dark:text-blue-300 shadow-md' : ($e['color'] === 'purple' ? 'bg-purple-50 dark:bg-purple-900 border-purple-500 text-purple-700 dark:text-purple-300 shadow-md' : 'bg-slate-100 dark:bg-slate-800 border-slate-400 text-slate-800 dark:text-white shadow-md')) }}'
                                       : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-400 dark:hover:border-slate-500'"
                                   class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center justify-center gap-2 text-center transition-all duration-200">
                                <input type="radio" name="etapa_consolidacion" value="{{ $e['value'] }}" x-bind:checked="etapa === '{{ $e['value'] }}'" class="sr-only">
                                <i class="fas {{ $e['icon'] }} text-xl"></i>
                                <span class="text-[11px] font-bold leading-tight">{{ $e['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-2 font-semibold"><i class="fas fa-info-circle mr-1"></i>Solo los miembros <strong>Bautizados</strong> pueden votar en elecciones.</p>
                    </div>

                    {{-- FECHAS DE INTEGRACIÓN, CONVERSIÓN Y BAUTISMOS --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 mb-6">
                        <div class="member-form-field flex flex-col">
                            <label class="member-form-label text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                <span><i class="fas fa-heart text-rose-500 mr-1"></i> Lugar de Conversión</span>
                            </label>
                            <input type="text" name="lugar_conversion" value="{{ old('lugar_conversion', $miembro->lugar_conversion) }}" placeholder="Ej: Iglesia Central, Campaña..."
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                        </div>
                        <div class="member-form-field flex flex-col">
                            <label class="member-form-label text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                <span><i class="fas fa-calendar-alt text-amber-500 mr-1"></i> Fecha de Conversión</span>
                            </label>
                            <input type="date" name="fecha_conversion" value="{{ old('fecha_conversion', $miembro->fecha_conversion ? $miembro->fecha_conversion->format('Y-m-d') : '') }}"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                        </div>
                        <div class="member-form-field flex flex-col">
                            <label class="member-form-label text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                <span><i class="fas fa-calendar-check text-emerald-500 mr-1"></i> Fecha de Integración</span>
                            </label>
                            <input type="date" name="fecha_integracion" value="{{ old('fecha_integracion', $miembro->fecha_integracion ? $miembro->fecha_integracion->format('Y-m-d') : '') }}"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                        </div>
                        <div class="member-form-field flex flex-col">
                            <label class="member-form-label text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">
                                <span><i class="fas fa-water text-blue-500 mr-1"></i> Fecha Bautismo en Aguas</span>
                            </label>
                            <input type="date" name="fecha_bautismo" value="{{ old('fecha_bautismo', $miembro->fecha_bautismo ? $miembro->fecha_bautismo->format('Y-m-d') : '') }}"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                        </div>
                    </div>

                    {{-- BAUTISMOS --}}
                    <div class="md:col-span-2 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label for="bautizado_agua" class="member-baptism-card flex items-center gap-3 w-full min-w-0 p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                <input id="bautizado_agua" name="bautizado_agua" type="checkbox" value="1" {{ old('bautizado_agua', $miembro->bautizado_agua) ? 'checked' : '' }} class="h-5 w-5 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-snug min-w-0">
                                    <i class="fas fa-water text-blue-500 mr-1.5"></i> Bautizado en Aguas
                                </span>
                            </label>
                            <label for="bautismo_espiritu_santo" class="member-baptism-card flex items-center gap-3 w-full min-w-0 p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                <input id="bautismo_espiritu_santo" name="bautismo_espiritu_santo" type="checkbox" value="1" {{ old('bautismo_espiritu_santo', $miembro->bautismo_espiritu_santo) ? 'checked' : '' }} class="h-5 w-5 shrink-0 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-snug min-w-0">
                                    <i class="fas fa-fire text-amber-500 mr-1.5"></i> Bautismo Espíritu Santo
                                </span>
                            </label>
                        </div>
                    </div>


                    {{-- ORGANIZACIONES / SOCIEDADES A LAS QUE PERTENECE --}}
                    <div class="md:col-span-2 mb-6">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Organizaciones / Sociedades a las que pertenece</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($organizaciones as $org)
                                @php
                                    $isAssigned = false;
                                    $puestoActual = 'Miembro';
                                    if(is_array(old('organizaciones')) && in_array($org->id, old('organizaciones'))) {
                                        $isAssigned = true;
                                        $puestoActual = old('puestos.'.$org->id, 'Miembro');
                                    } elseif (!is_array(old('organizaciones')) && $miembro->organizaciones->contains($org->id)) {
                                        $isAssigned = true;
                                        $puestoActual = $miembro->organizaciones->find($org->id)->pivot->puesto ?? 'Miembro';
                                    }
                                    $isDirectivoInit = ($puestoActual && strtolower($puestoActual) !== 'miembro') ? 'true' : 'false';
                                @endphp
                                <div class="p-3 border border-slate-200 dark:border-slate-800/80 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors" x-data="{ checked: {{ $isAssigned ? 'true' : 'false' }}, isDirectivo: {{ $isDirectivoInit }} }">
                                    <label class="flex items-center cursor-pointer mb-2">
                                        <input type="checkbox" name="organizaciones[]" value="{{ $org->id }}" x-model="checked"
                                               class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 transition-colors">
                                        <span class="ml-3 text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $org->nombre }}</span>
                                    </label>
                                    <div x-show="checked" x-transition x-cloak class="mt-2 flex flex-col gap-2">
                                        <select x-model="isDirectivo" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                            <option :value="false">Solo Miembro</option>
                                            <option :value="true">Es Directivo</option>
                                        </select>
                                        
                                        <input type="text" x-show="isDirectivo" x-bind:disabled="!isDirectivo" name="puestos[{{ $org->id }}]" value="{{ $puestoActual === 'Miembro' ? '' : $puestoActual }}" placeholder="Especifique cargo (Ej: Presidente)" 
                                               class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-amber-50/50 dark:bg-amber-900/10 px-3 py-2 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                                               
                                        <input type="hidden" x-bind:disabled="isDirectivo" name="puestos[{{ $org->id }}]" value="Miembro">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- MINISTERIOS EN LOS QUE SIRVE --}}
                    <div class="md:col-span-2 mb-6">
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Ministerios en los que sirve</label>
                        @if($ministerios->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($ministerios as $min)
                                    <label class="flex items-center p-3 border rounded-xl hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                                        <input type="checkbox" name="ministerios[]" value="{{ $min->id }}" 
                                        {{ (is_array(old('ministerios')) ? in_array($min->id, old('ministerios')) : (isset($miembro) && $miembro->ministerios->contains($min->id))) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $min->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 text-center">
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">No hay ministerios registrados en el sistema actualmente.</span>
                            </div>
                        @endif
                    </div>

                    {{-- LIDERAZGO ACTIVO --}}
                    <div class="md:col-span-2 mb-6" x-data="{ isLider: {{ old('es_lider', $miembro->es_lider ?? false) ? 'true' : 'false' }} }">
                        <div class="flex items-center">
                            <input id="es_lider" name="es_lider" type="checkbox" value="1" x-model="isLider" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="es_lider" class="ml-3 block text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                                Este miembro ejerce un cargo de Liderazgo activo.
                            </label>
                        </div>
                        <div x-show="isLider" x-transition x-cloak class="mt-4">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Especifique el cargo o área de liderazgo</label>
                            <select name="cargo_liderazgo" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-indigo-50/30 dark:bg-indigo-900/10 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm cursor-pointer appearance-none">
                                <option value="">Seleccione un cargo existente</option>
                                @foreach($cargos as $c)
                                    <option value="{{ $c }}" {{ old('cargo_liderazgo', $miembro->cargo_liderazgo ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: FOTOGRAFÍA --}}
            <div x-data="{ fileName: '', filePreview: '' }">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="stat-icon-box bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-500/20 shadow-sm flex-shrink-0">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">4. Fotografía de Perfil</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Imagen oficial para el carnet digital</p>
                    </div>
                </div>

                <div class="p-8 rounded-3xl border-2 border-dashed border-purple-300 dark:border-purple-700/60 bg-purple-50/30 dark:bg-purple-950/10 hover:bg-purple-50/60 dark:hover:bg-purple-950/20 transition-all flex flex-col items-center justify-center gap-4 text-center group cursor-pointer relative"
                     @click="$refs.fotoInput.click()">
                    
                    <input type="file" name="foto" x-ref="fotoInput" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif"
                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''; 
                                    if($event.target.files[0]) { 
                                        let reader = new FileReader(); 
                                        reader.onload = (e) => filePreview = e.target.result; 
                                        reader.readAsDataURL($event.target.files[0]); 
                                    }">

                    <!-- Vista previa dinámica de la nueva imagen seleccionada -->
                    <template x-if="filePreview">
                        <div class="relative mb-2">
                            <img :src="filePreview" class="w-32 h-32 rounded-2xl object-cover border-4 border-white dark:border-slate-800 shadow-xl mx-auto animate-fade-in">
                            <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold pointer-events-none">Cambiar Foto</div>
                        </div>
                    </template>

                    <!-- Vista previa de foto actual (cuando no se ha seleccionado una nueva) -->
                    @if($miembro->foto)
                        <template x-if="!filePreview">
                            <div class="relative mb-2">
                                <img src="{{ asset('storage/miembros/' . $miembro->foto) }}" 
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($miembro->nombres . ' ' . $miembro->apellidos) }}&background=0D8abc&color=fff&size=200'"
                                     class="w-32 h-32 rounded-2xl object-cover border-4 border-white dark:border-slate-800 shadow-xl mx-auto">
                                <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-xs font-bold pointer-events-none gap-1">
                                    <i class="fas fa-camera text-lg"></i>
                                    <span>Cambiar Foto</span>
                                </div>
                            </div>
                        </template>
                    @else
                        <template x-if="!filePreview">
                            <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl shadow-sm group-hover:scale-110 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-all duration-300">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                        </template>
                    @endif

                    <div>
                        <template x-if="!fileName">
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Haz clic para buscar o arrastra una imagen aquí</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium mb-0">PNG, JPG o GIF (Máximo 2MB)</p>
                            </div>
                        </template>
                        <template x-if="fileName">
                            <div class="bg-white dark:bg-slate-800 px-4 py-2 rounded-xl border border-purple-200 dark:border-purple-800/80 shadow-sm inline-flex items-center gap-2 max-w-xs">
                                <i class="fas fa-file-image text-purple-500 text-sm flex-shrink-0"></i>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate" x-text="fileName"></span>
                                <button type="button" @click.stop="fileName = ''; filePreview = ''; $refs.fotoInput.value = ''" class="text-slate-400 hover:text-rose-500 transition-colors ml-1">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Botones de Guardado -->
            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('miembros.show', $miembro->id) }}" class="px-6 py-3.5 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all no-underline cursor-pointer">Cancelar</a>
                <button type="submit" class="btn-bento-primary px-8 py-3.5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                    <i class="fas fa-save text-base"></i>
                    <span>Guardar Cambios del Miembro</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#familia_select').select2({
            placeholder: "Seleccionar Familia (Opcional)",
            allowClear: true,
            width: '100%'
        }).on('select2:select select2:unselect', function (e) {
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });

        $('#conyuge_select').select2({
            placeholder: "Seleccionar Cónyuge (Opcional)",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
