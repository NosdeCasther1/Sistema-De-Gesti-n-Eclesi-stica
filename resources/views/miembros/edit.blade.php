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
</style>
@endpush

@section('header_title', 'Edición de Miembro')
@section('header_subtitle', 'Actualización de expediente y datos de contacto')
@section('header_icon')
<i class="fas fa-user-edit fs-5"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-5xl mx-auto">
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
                        <input type="text" name="telefono" value="{{ old('telefono', $miembro->telefono) }}" placeholder="Ej: 5555-1234"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $miembro->email) }}" placeholder="correo@ejemplo.com"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Dirección Residencial</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $miembro->direccion) }}" placeholder="Ej: 4ta Calle 5-20 Zona 1"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Ciudad / Municipio</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad', $miembro->ciudad) }}" placeholder="Ej: Guatemala"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
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
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Ministerio</label>
                        <input type="text" name="ministerio" value="{{ old('ministerio', $miembro->ministerio) }}" placeholder="Ej: Alabanza, Jóvenes, Damas..."
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Etapa de Consolidación</label>
                        <select name="etapa_consolidacion" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm cursor-pointer">
                            <option value="Nuevo"             {{ $miembro->etapa_consolidacion == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                            <option value="En Discipulado"    {{ $miembro->etapa_consolidacion == 'En Discipulado' ? 'selected' : '' }}>En Discipulado</option>
                            <option value="Asignado a Célula" {{ $miembro->etapa_consolidacion == 'Asignado a Célula' ? 'selected' : '' }}>Asignado a Célula</option>
                            <option value="Bautizado"         {{ $miembro->etapa_consolidacion == 'Bautizado' ? 'selected' : '' }}>Bautizado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Fecha de Integración / Bautismo</label>
                        <input type="date" name="fecha_integracion" value="{{ old('fecha_integracion', $miembro->fecha_integracion ? $miembro->fecha_integracion->format('Y-m-d') : '') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Familia (Núcleo Familiar)</label>
                        <select name="familia_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm cursor-pointer">
                            <option value="">Seleccionar Familia (Opcional)</option>
                            @foreach(\App\Models\Familia::orderBy('nombre')->get() as $f)
                                <option value="{{ $f->id }}" {{ old('familia_id', $miembro->familia_id) == $f->id ? 'selected' : '' }}>{{ $f->nombre }}</option>
                            @endforeach
                        </select>
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
