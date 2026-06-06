@extends('layouts.app')

@section('title', 'Organizaciones')
@section('header_icon')<i class="fas fa-sitemap"></i>@endsection
@section('header_title', 'Organizaciones')
@section('header_subtitle', 'Gestión de comités ministeriales, directivas y procesos de elección.')

@section('content')
{{-- CONTENEDOR MAESTRO REACTIVO DE ALPINE.JS --}}
<div x-data="organizacionesModule(
    {{ isset($eleccionActiva) && $eleccionActiva ? $eleccionActiva->id : 'null' }}, 
    {{ auth()->check() && auth()->user()->can('gestionar-elecciones') ? 'true' : 'false' }}, 
    '{{ csrf_token() }}', 
    '{{ isset($eleccionActiva) && $eleccionActiva ? $eleccionActiva->fecha_fin : '' }}', 
    {{ $organizacionSeleccionada ? $organizacionSeleccionada->id : 'null' }}
)" 
class="relative p-4 lg:p-8 transition-colors duration-300 antialiased"
x-bind:class="procesando ? 'opacity-70 pointer-events-none' : ''">

    {{-- SISTEMA DE NOTIFICACIONES (TOAST FLOTANTE) --}}
    <div x-show="toastVisible" x-cloak x-transition
         class="fixed bottom-5 right-5 z-[9999] px-4 py-3 rounded-xl shadow-lg border backdrop-blur-md text-sm font-bold flex items-center gap-2 transition-all duration-300"
         x-bind:class="toastTipo === 'error' ? 'bg-rose-100 border-rose-200 text-rose-800 dark:bg-rose-950/90 dark:border-rose-900 dark:text-rose-200' : 'bg-emerald-100 border-emerald-200 text-emerald-800 dark:bg-emerald-950/90 dark:border-emerald-900 dark:text-emerald-200'">
        <i x-bind:class="toastTipo === 'error' ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check'"></i>
        <span x-text="toastMensaje"></span>
    </div>

    <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- COLUMNA IZQUIERDA: Padrón y Datos (8 Columnas) --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- BLOQUE TÍTULO ESTABILIZADO --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                        <i class="fa-solid fa-layer-group text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-xl font-black text-[var(--text-main)] uppercase tracking-tight">Gestión de Organizaciones</h1>
                        <p class="text-xs text-muted mt-1 font-medium">Panel de control de directivas, miembros activos y asambleas.</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/60">
                    <label class="block text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2">
                        <i class="fa-solid fa-sitemap mr-1"></i> Organización / Comité a Gestionar
                    </label>
                    <select onchange="window.location.href='?org='+this.value" 
                            class="block w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-[var(--bg-body)] text-[var(--text-main)] text-sm py-3 px-4 font-bold outline-none cursor-pointer focus:ring-2 focus:ring-indigo-500/20">
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ (isset($organizacionSeleccionada) && $organizacionSeleccionada->id == $org->id) ? 'selected' : '' }}>
                                {{ $org->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(isset($organizacionSeleccionada))
                {{-- REEMPLAZAR TARJETA DE ORGANIZACIÓN ACTIVA --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[9px] font-black rounded uppercase tracking-wider border border-emerald-200 dark:border-emerald-500/20">Activa</span>
                            <h2 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight">{{ $organizacionSeleccionada->nombre }}</h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xl">{{ $organizacionSeleccionada->descripcion }}</p>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="{{ route('organizaciones.reporte_miembros', $organizacionSeleccionada->id) }}" target="_blank" class="shrink-0 w-full sm:w-auto px-5 py-2.5 bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 rounded-xl font-bold hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors flex items-center justify-center text-xs shadow-sm">
                            <i class="fa-solid fa-file-pdf mr-2"></i> Reporte Miembros
                        </a>
                        <template x-if="esAdmin">
                            <button @click="mostrarModalPadron = true" class="shrink-0 w-full sm:w-auto px-5 py-2.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 rounded-xl font-bold hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors flex items-center justify-center text-xs shadow-sm">
                                <i class="fa-solid fa-users mr-2"></i> Padrón Electoral
                            </button>
                        </template>
                    </div>
                </div>

                {{-- BENTO GRID DE MIEMBROS CON CONTRASTE PROTEGIDO --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($padronMiembros as $miembro)
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 flex items-center shadow-sm hover:shadow-md transition-all duration-200 group">
                            <div class="h-12 w-12 rounded-xl bg-[var(--bg-body)] flex items-center justify-center text-[var(--text-main)] font-black text-sm shrink-0 border border-slate-200 dark:border-slate-850 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                {{ strtoupper(substr($miembro->nombres, 0, 1) . substr($miembro->apellidos, 0, 1)) }}
                            </div>
                            <div class="ml-4 flex-1 overflow-hidden">
                                {{-- Agrupación compacta --}}
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h4>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-1.5 py-0.5 rounded shrink-0">
                                        {{ $miembro->pivot->puesto ?? 'MIEMBRO' }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium"><i class="fa-regular fa-calendar mr-1"></i>Asignado: {{ $miembro->pivot->fecha_asignacion ? \Carbon\Carbon::parse($miembro->pivot->fecha_asignacion)->format('d M, Y') : ($miembro->pivot->created_at ? \Carbon\Carbon::parse($miembro->pivot->created_at)->format('d M, Y') : '21 May, 2026') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- COLUMNA DERECHA: Formulario / Elección Activa (4 Columnas) --}}
        <div class="lg:col-span-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            @if(!isset($eleccionActiva) || !$eleccionActiva)
                {{-- PANEL DE NUEVA CONVOCATORIA --}}
                <div class="flex flex-col items-center justify-center text-center py-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-4 border border-indigo-100 dark:border-indigo-500/20">
                        <i class="fa-solid fa-box-open text-2xl text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <h3 class="text-base font-black text-[var(--text-main)] mb-1">Nueva Convocatoria</h3>
                    <p class="text-xs text-muted mb-6 max-w-[240px]">Abre un nuevo proceso de votación confidencial.</p>
                    
                    <div class="w-full space-y-4 bg-[var(--bg-body)] p-4 rounded-xl border border-slate-100 dark:border-slate-800/60 text-left">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Título de Elección</label>
                            <input type="text" x-model="nuevaEleccionTitulo" @keyup.enter="iniciarEleccion()" placeholder="Ej: Directiva Jóvenes 2026" class="block w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-[var(--text-main)] text-xs py-2.5 px-3 outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Regla de Escrutinio</label>
                            <select x-model="nuevaEleccionTipoMayoria" class="block w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-[var(--text-main)] text-xs py-2.5 px-3 outline-none cursor-pointer focus:ring-1 focus:ring-indigo-500">
                                <option value="simple">Mayoría Simple</option>
                                <option value="absoluta">Mitad más 1 (Absoluta)</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Duración del Proceso</label>
                            <select x-model="nuevaEleccionHoras" class="block w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-[var(--text-main)] text-xs py-2.5 px-3 outline-none cursor-pointer focus:ring-1 focus:ring-indigo-500">
                                <option value="1">1 Hora</option>
                                <option value="2">2 Horas</option>
                                <option value="4">4 Horas</option>
                                <option value="8">8 Horas</option>
                                <option value="24">24 Horas</option>
                                <option value="48">48 Horas</option>
                            </select>
                        </div>
                        <button type="button" @click="iniciarEleccion()" :disabled="!nuevaEleccionTitulo.trim() || procesando" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all text-xs mt-2 border border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            Iniciar Proceso Oficial
                        </button>
                    </div>
                </div>
            @else
                {{-- PANEL DE ELECCIÓN ACTIVA --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">En Curso</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 rounded border border-rose-100 dark:border-rose-900/50 flex items-center gap-1">
                            <i class="fa-solid fa-clock text-[9px] animate-pulse"></i>
                            <span x-text="tiempoRestante.horas + ':' + tiempoRestante.minutos + ':' + tiempoRestante.segundos">00:00:00</span>
                        </span>
                    </div>
                    
                    <h3 class="text-base font-black text-[var(--text-main)] mb-1 truncate">{{ $eleccionActiva->titulo }}</h3>
                    <div class="mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 text-indigo-700 dark:text-indigo-450 text-[10px] font-black uppercase tracking-wider">
                            <i class="fa-solid fa-sitemap"></i>
                            <span>{{ $eleccionActiva->organizacion->nombre }}</span>
                        </span>
                    </div>
                    <p class="text-[10px] text-muted font-bold uppercase tracking-wider mb-4">
                        {{ $eleccionActiva->tipo_mayoria === 'simple' ? 'Mayoría Simple' : 'Mayoría Absoluta' }}
                    </p>

                    <div class="my-4 border-t border-slate-100 dark:border-slate-800"></div>

                    {{-- ESTADO DE LA RONDA ACTUAL --}}
                    @if($eleccionActiva->puesto_en_curso)
                        <div class="space-y-4">
                            <div class="bg-[var(--bg-body)] p-4 rounded-xl border border-slate-100 dark:border-slate-800/60">
                                <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Ronda Abierta para</span>
                                <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase">{{ $eleccionActiva->puesto_en_curso }}</h4>
                            </div>

                            {{-- PIN Gigante --}}
                            <div class="relative px-6 py-4 bg-slate-950 text-center rounded-2xl border border-slate-900 shadow-inner">
                                <span class="text-[9px] font-black text-amber-400 uppercase tracking-widest block mb-1">PIN DE ACCESO</span>
                                <span class="text-4xl font-extrabold tracking-widest text-amber-400 select-all font-mono leading-none">{{ $eleccionActiva->pin_ronda }}</span>
                            </div>

                            <p class="text-[10px] text-muted leading-relaxed text-center"><i class="fa-solid fa-mobile-screen-button mr-1"></i> Los votantes deben usar el PIN en su cabina móvil.</p>

                            {{-- FILA 1: Proyectar PIN + Cargar Boletas --}}
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('elecciones.proyector.pin', $eleccionActiva->id) }}" target="_blank"
                                        class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 shadow-sm active:scale-[0.98]">
                                    <i class="fa-solid fa-display"></i> Proyectar PIN
                                </a>
                                <button type="button" @click="mostrarModalManuales = true" 
                                        class="w-full py-3 border font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98]"
                                        style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                                    <i class="fa-solid fa-box-ballot"></i> Cargar Boletas
                                </button>
                            </div>

                            {{-- FILA 2: Generar nuevo PIN --}}
                            <button type="button" @click="generarNuevoPin()" 
                                    class="w-full py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/25 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                                <i class="fa-solid fa-arrows-rotate"></i> Generar Nuevo PIN
                            </button>

                            {{-- CERRAR RONDA --}}
                            <button type="button" @click="confirmarCerrarRonda()" 
                                    class="w-full py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/25 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 active:scale-[0.98]">
                                <i class="fa-solid fa-circle-stop text-[10px]"></i> Cerrar Ronda Actual
                            </button>
                        </div>
                    @else
                        <div class="space-y-3">
                            <div class="bg-[var(--bg-body)] p-4 rounded-xl border border-slate-100 dark:border-slate-800/60 text-center">
                                <i class="fa-solid fa-folder-open text-slate-400 text-xl mb-2 block"></i>
                                <p class="text-xs text-[var(--text-main)] font-bold">No hay ronda activa</p>
                                <p class="text-[10px] text-muted mt-0.5">Selecciona un puesto a continuación para abrir votación.</p>
                            </div>

                            <div class="space-y-3">
                                <select x-model="puestoSeleccionado" class="block w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-[var(--bg-body)] text-[var(--text-main)] text-xs py-2.5 px-3 outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">-- Seleccionar Cargo --</option>
                                    @foreach($puestosDisponibles as $puesto)
                                        <option value="{{ $puesto }}">{{ $puesto }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="abrirRonda()" :disabled="!puestoSeleccionado || procesando" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    Abrir Ronda de Votación
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="my-4 border-t border-slate-100 dark:border-slate-800"></div>

                    {{-- ACCIONES DE CONTROL GENERAL --}}
                    <div class="space-y-2">
                        <button type="button" @click="mostrarModalCandidatos = true" 
                                class="w-full py-2.5 border font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98]"
                                style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                            <i class="fa-solid fa-user-pen text-indigo-500"></i> Gestionar Candidatos
                        </button>
                        <a href="{{ route('elecciones.live', $eleccionActiva->id) }}" target="_blank"
                                class="w-full py-2.5 border font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98]"
                                style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                            <i class="fa-solid fa-circle-play text-emerald-500"></i> Proyectar Resultados
                        </a>
                        <a href="{{ route('elecciones.reporte', $eleccionActiva->id) }}" target="_blank" 
                           class="w-full py-2.5 border font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98]"
                           style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i> Acta de Escrutinio (PDF)
                        </a>
                        <button type="button" @click="mostrarModalDetener = true" class="w-full py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/25 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                            <i class="fa-solid fa-power-off"></i> Detener Elección General
                        </button>
                    </div>
                </div>
            @endif
        {{-- MODAL 1: GESTIÓN DE PADRÓN --}}
    <div x-show="mostrarModalPadron" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="w-full max-w-6xl max-h-[85vh] rounded-3xl shadow-2xl p-6 md:p-8 flex flex-col border transition-all duration-300"
             style="background-color: var(--bg-card); border-color: var(--border-color);">
            <div class="flex justify-between items-start pb-4 border-b shrink-0" style="border-color: var(--border-color);">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shrink-0">
                        <i class="fa-solid fa-user-group text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[var(--text-main)] tracking-tight">Padrón Electoral</h3>
                        <p class="text-xs text-muted mt-0.5 font-medium">Define los miembros activos y bautizados con derecho al voto en esta asamblea.</p>
                    </div>
                </div>
                <button @click="mostrarModalPadron = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border-0 bg-transparent cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            {{-- Filtros: búsqueda + organización --}}
            <div class="py-4 flex flex-col gap-4 shrink-0">
                {{-- Búsqueda expandida y centrada --}}
                <div class="relative flex items-center w-full max-w-2xl mx-auto">
                    <span class="absolute left-4.5 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-magnifying-glass text-base"></i></span>
                    <input type="text" x-model="busquedaPadron" placeholder="Buscar miembro por nombre o apellido..." 
                           class="w-full rounded-2xl border text-base py-3.5 pl-12 pr-12 outline-none focus:ring-2 focus:ring-indigo-500/25 transition-all placeholder-slate-400 dark:placeholder-slate-500 shadow-sm"
                           style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                    <!-- Botón para eliminar el texto de uno solo -->
                    <button x-show="busquedaPadron.length > 0" @click="busquedaPadron = ''" type="button"
                            class="absolute right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 bg-transparent border-0 cursor-pointer flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-lg"></i>
                    </button>
                </div>

                {{-- Chips de organización envueltos (flex-wrap) para mostrar todos --}}
                <div class="flex flex-wrap items-center justify-center gap-2 w-full pt-1" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; width: 100%;">
                    <button type="button" @click="filtroOrgPadron = ''"
                            :class="filtroOrgPadron === '' 
                                ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' 
                                : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 dark:bg-slate-800/50 dark:text-slate-400 dark:border-slate-700/50 dark:hover:bg-slate-700/30'"
                            class="px-3.5 py-2 rounded-xl border text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer shrink-0">
                        Todas
                    </button>
                    @foreach($todasLasOrganizaciones as $org)
                    <button type="button" @click="filtroOrgPadron = '{{ $org->id }}'"
                            :class="filtroOrgPadron === '{{ $org->id }}' 
                                ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' 
                                : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 dark:bg-slate-800/50 dark:text-slate-400 dark:border-slate-700/50 dark:hover:bg-slate-700/30'"
                            class="px-3.5 py-2 rounded-xl border text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer shrink-0">
                        {{ $org->nombre }}
                        <span class="ml-1 opacity-60">({{ $org->miembros->count() }})</span>
                    </button>
                    @endforeach
                </div>
            </div>
 
            <div class="overflow-y-auto max-h-[50vh] pr-2 custom-scrollbar grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 content-start pt-1 pb-4">
                @foreach($todosLosMiembros as $m)
                    <label x-show="
                               (busquedaPadron === '' || removerAcentos('{{ addslashes($m->nombres . ' ' . $m->apellidos) }}').includes(removerAcentos(busquedaPadron))) &&
                               (filtroOrgPadron === '' || {{ json_encode($m->organizaciones->pluck('id')->toArray()) }}.includes(parseInt(filtroOrgPadron)))
                           "
                           class="flex items-center gap-3.5 p-4 border rounded-2xl cursor-pointer transition-all duration-200"
                           :class="padronSeleccionado.includes({{ $m->id }}) 
                               ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/40 shadow-md ring-2 ring-indigo-500/10' 
                               : 'bg-[var(--bg-body)] border-[var(--border-color)] hover:bg-slate-50 dark:hover:bg-slate-800/40 hover:scale-[1.01] hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-500/30'">
                        <input type="checkbox" :value="{{ $m->id }}" x-model="padronSeleccionado" class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 h-4.5 w-4.5 cursor-pointer">
                        <div class="h-10 w-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 transition-colors duration-250"
                             :class="padronSeleccionado.includes({{ $m->id }}) ? 'bg-indigo-600 !text-white dark:bg-indigo-500 dark:!text-slate-950 shadow-md scale-95' : 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'">
                            {{ strtoupper(substr($m->nombres, 0, 1) . substr($m->apellidos, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-xs font-black text-[var(--text-main)] block truncate leading-snug">{{ $m->nombres }} {{ $m->apellidos }}</span>
                            @if($m->organizaciones->isNotEmpty())
                                <span class="text-[9px] text-muted truncate block mt-0.5 font-semibold">{{ $m->organizaciones->pluck('nombre')->join(' • ') }}</span>
                            @endif
                        </div>
                        @if(in_array($m->id, $padronMiembros->pluck('id')->toArray()))
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-wider shrink-0 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-200/30 dark:border-emerald-500/20">Padrón</span>
                        @endif
                    </label>
                @endforeach
            </div>
 
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex justify-end gap-3 mt-4 shrink-0">
                <button @click="mostrarModalPadron = false" 
                        class="px-5 py-2.5 border font-bold rounded-xl text-xs transition-all bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/30 hover:-translate-y-0.5 active:scale-[0.97] cursor-pointer text-[var(--text-main)] border-[var(--border-color)]">
                    Cancelar
                </button>
                <button @click="guardarPadron()" 
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all hover:-translate-y-0.5 hover:shadow-lg active:scale-[0.97] cursor-pointer">
                    Guardar Padrón
                </button>
            </div>
        </div>
    </div>
    {{-- MODAL 2: GESTIÓN DE CANDIDATOS --}}
    <div x-show="mostrarModalCandidatos" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="w-full max-w-6xl max-h-[85vh] rounded-3xl shadow-2xl p-6 md:p-8 flex flex-col border transition-all duration-300"
             style="background-color: var(--bg-card); border-color: var(--border-color);">
            <div class="flex justify-between items-start pb-4 border-b shrink-0" style="border-color: var(--border-color);">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shrink-0">
                        <i class="fa-solid fa-user-tie text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[var(--text-main)] tracking-tight">Postulación de Candidatos</h3>
                        <p class="text-xs text-muted mt-0.5 font-medium">Asigna y postula a los miembros del padrón a los respectivos cargos directivos.</p>
                    </div>
                </div>
                <button @click="mostrarModalCandidatos = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border-0 bg-transparent cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <div class="py-4 shrink-0 flex justify-center">
                <div class="relative flex items-center w-full max-w-2xl">
                    <span class="absolute left-4.5 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-magnifying-glass text-base"></i></span>
                    <input type="text" x-model="busquedaCandidatos" placeholder="Buscar miembro en el padrón..." 
                           class="w-full rounded-2xl border text-base py-3.5 pl-12 pr-12 outline-none focus:ring-2 focus:ring-indigo-500/25 transition-all placeholder-slate-400 dark:placeholder-slate-500 shadow-sm"
                           style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                    <!-- Botón para eliminar el texto de uno solo -->
                    <button x-show="busquedaCandidatos.length > 0" @click="busquedaCandidatos = ''" type="button"
                            class="absolute right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 bg-transparent border-0 cursor-pointer flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto max-h-[50vh] pr-2 custom-scrollbar grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 content-start pt-1 pb-4">
                @foreach($padronMiembros as $m)
                    <div x-show="busquedaCandidatos === '' || removerAcentos('{{ addslashes($m->nombres . ' ' . $m->apellidos) }}').includes(removerAcentos(busquedaCandidatos))"
                         class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border rounded-2xl gap-3 transition-all duration-200"
                         :class="isCandidatoChecked({{ $m->id }}) 
                             ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/40 shadow-md ring-2 ring-indigo-500/10' 
                             : 'bg-[var(--bg-body)] border-[var(--border-color)] hover:bg-slate-50 dark:hover:bg-slate-800/40 hover:scale-[1.01] hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-500/30'">
                        <label class="flex items-center gap-3 cursor-pointer select-none flex-1 min-w-0">
                            <input type="checkbox" :checked="isCandidatoChecked({{ $m->id }})" @change="toggleCandidato({{ $m->id }})" class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 h-4.5 w-4.5 cursor-pointer">
                            <div class="h-10 w-10 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 transition-colors duration-250"
                                 :class="isCandidatoChecked({{ $m->id }}) ? 'bg-indigo-600 !text-white dark:bg-indigo-500 dark:!text-slate-950 shadow-md scale-95' : 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'">
                                {{ strtoupper(substr($m->nombres, 0, 1) . substr($m->apellidos, 0, 1)) }}
                            </div>
                            <span class="text-xs font-black text-[var(--text-main)] truncate leading-snug">{{ $m->nombres }} {{ $m->apellidos }}</span>
                        </label>
                        
                        <div x-show="isCandidatoChecked({{ $m->id }})" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="w-full sm:w-auto flex items-center gap-1.5 shrink-0 mt-2 sm:mt-0">
                            <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider shrink-0">Cargo:</span>
                            <select :value="getCandidatoPost({{ $m->id }})" @change="updateCandidatoPost({{ $m->id }}, $event.target.value)" 
                                    class="text-xs py-1.5 px-3 rounded-lg border font-bold cursor-pointer outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm"
                                    style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                                <option value="Presidente">Presidente</option>
                                <option value="Vicepresidente">Vicepresidente</option>
                                <option value="Secretario">Secretario</option>
                                <option value="Tesorero">Tesorero</option>
                                <option value="Vocal 1">Vocal 1</option>
                                <option value="Vocal 2">Vocal 2</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex justify-end gap-3 mt-4 shrink-0">
                <button @click="mostrarModalCandidatos = false" 
                        class="px-5 py-2.5 border font-bold rounded-xl text-xs transition-all bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/30 hover:-translate-y-0.5 active:scale-[0.97] cursor-pointer text-[var(--text-main)] border-[var(--border-color)]">
                    Cancelar
                </button>
                <button @click="guardarCandidatos()" 
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all hover:-translate-y-0.5 hover:shadow-lg active:scale-[0.97] cursor-pointer">
                    Guardar Candidatos
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 3: CONFIRMACIÓN DETENER ELECCIÓN --}}
    <div x-show="mostrarModalDetener" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center border"
             style="background-color: var(--bg-card); border-color: var(--border-color);">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/30 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100 dark:border-rose-900/50">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-[var(--text-main)] mb-1">¿Finalizar Elección General?</h3>
            <p class="text-xs text-muted mb-5 leading-relaxed">Esta acción detendrá definitivamente todo el proceso electoral de forma irreversible. Se calcularán los resultados finales.</p>
            
            <div class="flex justify-center gap-2">
                <button @click="mostrarModalDetener = false" 
                        class="px-4 py-2 border font-bold rounded-xl text-xs transition-all hover:opacity-90 active:scale-[0.98]"
                        style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                    Cancelar
                </button>
                <button @click="detenerEleccion()" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold">Sí, Finalizar Elección</button>
            </div>
        </div>
    </div>

    {{-- MODAL 4: CONFIRMACIÓN CERRAR RONDA --}}
    <div x-show="mostrarModalCerrarRonda" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center border"
             style="background-color: var(--bg-card); border-color: var(--border-color);">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/30 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100 dark:border-amber-900/50">
                <i class="fa-solid fa-circle-exclamation text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-[var(--text-main)] mb-1">¿Cerrar Ronda de Votación?</h3>
            <p class="text-xs text-muted mb-5 leading-relaxed">Se destruirá el PIN actual de votación. Se bloqueará el ingreso de nuevos sufragios para este cargo de manera definitiva.</p>
            
            <div class="flex justify-center gap-2">
                <button @click="mostrarModalCerrarRonda = false" 
                        class="px-4 py-2 border font-bold rounded-xl text-xs transition-all hover:opacity-90 active:scale-[0.98]"
                        style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                    Cancelar
                </button>
                <button @click="cerrarRonda()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold">Sí, Cerrar Ronda</button>
            </div>
        </div>
    </div>    <div x-show="mostrarModalManuales" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-cloak x-transition>
        <div class="w-full max-w-6xl max-h-[85vh] rounded-3xl shadow-2xl p-6 md:p-8 flex flex-col border transition-all duration-300"
             style="background-color: var(--bg-card); border-color: var(--border-color);">
            <div class="flex justify-between items-start pb-4 border-b shrink-0" style="border-color: var(--border-color);">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shrink-0">
                        <i class="fa-solid fa-clipboard-check text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[var(--text-main)] tracking-tight">Escrutinio Manual / Físico</h3>
                        <p class="text-xs text-muted mt-0.5 font-medium">Registra los votos emitidos físicamente en boletas tradicionales para esta ronda.</p>
                    </div>
                </div>
                <button @click="mostrarModalManuales = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border-0 bg-transparent cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="overflow-y-auto max-h-[55vh] py-4 pr-1 custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start pb-2">
                    {{-- Sección 1: Votantes físicos con búsqueda --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block">1. Selecciona los Votantes Físicos</label>
                        {{-- Buscador de votante --}}
                        <div class="relative flex items-center w-full max-w-2xl">
                            <span class="absolute left-4.5 text-slate-400 dark:text-slate-500"><i class="fa-solid fa-magnifying-glass text-base"></i></span>
                            <input type="text" x-model="busquedaVotanteManual" placeholder="Buscar votante por nombre..."
                                   class="w-full rounded-2xl border text-base py-3.5 pl-12 pr-12 outline-none focus:ring-2 focus:ring-indigo-500/25 transition-all placeholder-slate-400 dark:placeholder-slate-500 shadow-sm"
                                   style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                            <!-- Botón para eliminar el texto de uno solo -->
                            <button x-show="busquedaVotanteManual.length > 0" @click="busquedaVotanteManual = ''" type="button"
                                    class="absolute right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 bg-transparent border-0 cursor-pointer flex items-center justify-center">
                                <i class="fa-solid fa-circle-xmark text-lg"></i>
                            </button>
                        </div>
                        <div class="p-3.5 rounded-2xl border space-y-2.5 max-h-[50vh] overflow-y-auto custom-scrollbar"
                             style="background-color: var(--bg-body); border-color: var(--border-color);">
                            @foreach($padronMiembros as $m)
                                <label x-show="busquedaVotanteManual === '' || removerAcentos('{{ addslashes($m->nombres . ' ' . $m->apellidos) }}').includes(removerAcentos(busquedaVotanteManual))"
                                       class="flex items-center gap-3 p-2.5 rounded-xl border cursor-pointer transition-all duration-200"
                                       :class="votantesManuales.includes({{ $m->id }}) 
                                           ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/40 shadow-sm ring-1 ring-indigo-500/10' 
                                           : 'bg-[var(--bg-body)] border-[var(--border-color)] hover:bg-slate-50 dark:hover:bg-slate-800/40'"
                                       style="color: var(--text-main);">
                                    <input type="checkbox" :value="{{ $m->id }}" x-model="votantesManuales" class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 h-4 w-4 cursor-pointer">
                                    <div class="h-8 w-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 transition-colors duration-250"
                                         :class="votantesManuales.includes({{ $m->id }}) ? 'bg-indigo-600 !text-white dark:bg-indigo-500 dark:!text-slate-950 shadow-md scale-95' : 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'">
                                        {{ strtoupper(substr($m->nombres, 0, 1) . substr($m->apellidos, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-black truncate leading-snug">{{ $m->nombres }} {{ $m->apellidos }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sección 2: Candidatos y Auditoría --}}
                    <div class="space-y-4">
                        @if($eleccionActiva && $eleccionActiva->puesto_en_curso)
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block">2. Asigna los votos ({{ $eleccionActiva->puesto_en_curso }})</label>
                                <div class="space-y-3 p-3.5 rounded-2xl border max-h-[44vh] overflow-y-auto custom-scrollbar" style="background-color: var(--bg-body); border-color: var(--border-color);">
                                    @foreach($candidatos as $cand)
                                        @if($cand->puesto_postulado === $eleccionActiva->puesto_en_curso)
                                            <div class="flex justify-between items-center gap-4 border p-2.5 rounded-xl bg-[var(--bg-card)] border-[var(--border-color)]">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-xs shrink-0 border border-indigo-100/50 dark:border-indigo-500/10">
                                                        {{ strtoupper(substr($cand->miembro->nombres, 0, 1) . substr($cand->miembro->apellidos, 0, 1)) }}
                                                    </div>
                                                    <span class="text-xs font-black text-[var(--text-main)] truncate max-w-[160px] leading-snug">{{ $cand->miembro->nombres }} {{ $cand->miembro->apellidos }}</span>
                                                </div>
                                                <input type="number" x-model="votosCandidatos[{{ $cand->id }}]" min="0" placeholder="0" 
                                                       class="w-20 text-center text-xs font-bold rounded-xl border py-2 px-1 outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                                       style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Auditoría rápida --}}
                        <div class="bg-indigo-50/50 dark:bg-indigo-950/10 p-4 rounded-2xl border border-indigo-100/50 dark:border-indigo-950/30 space-y-2">
                            <div class="flex justify-between items-center text-xs font-bold text-[var(--text-main)]">
                                <span>Balotas Físicas Marcadas:</span>
                                <span x-text="votantesManuales.length" class="text-indigo-600 dark:text-indigo-400 text-sm"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-bold text-[var(--text-main)]">
                                <span>Votos Asignados en Urna:</span>
                                <span x-text="Object.values(votosCandidatos).reduce((a, b) => parseInt(a || 0) + parseInt(b || 0), 0)" 
                                      x-bind:class="Object.values(votosCandidatos).reduce((a, b) => parseInt(a || 0) + parseInt(b || 0), 0) > votantesManuales.length ? 'text-rose-500' : 'text-emerald-500'"
                                      class="text-sm"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex justify-end gap-3 mt-4 shrink-0">
                <button @click="mostrarModalManuales = false" 
                        class="px-5 py-2.5 border font-bold rounded-xl text-xs transition-all bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/30 hover:-translate-y-0.5 active:scale-[0.97] cursor-pointer text-[var(--text-main)] border-[var(--border-color)]">
                    Cancelar
                </button>
                <button @click="guardarVotosManuales()" 
                        x-bind:disabled="procesando || Object.values(votosCandidatos).reduce((a,b) => parseInt(a||0) + parseInt(b||0), 0) > votantesManuales.length"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all hover:-translate-y-0.5 hover:shadow-lg active:scale-[0.97] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Sellar Votos Físicos
                </button>
            </div>
        </div>
    </div>

    <div id="proyectorOverlay" x-show="proyeccionResultadosActiva" x-cloak
         class="fixed inset-0 z-[9999] bg-[#0b1120] text-slate-100 flex flex-col justify-center items-center p-8 select-none">
        <button @click="salirProyeccion()" class="absolute top-4 right-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold shadow text-xs transition-all flex items-center gap-2 border border-slate-700">
            <i class="fa-solid fa-window-restore"></i> Salir de Proyección
        </button>
        
        <div class="text-center max-w-4xl w-full">
            <p class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-3">Cabina de Votación Oficial</p>
            <h2 class="text-4xl font-extrabold tracking-tight text-white mb-8">Asamblea General — AD Rey de Reyes</h2>
            
            @if(isset($eleccionActiva) && $eleccionActiva)
                @if($eleccionActiva->puesto_en_curso)
                    <p class="text-slate-400 text-lg mb-2">Para votar por el cargo de:</p>
                    <h3 class="text-5xl font-black text-indigo-300 uppercase tracking-tight mb-10">{{ $eleccionActiva->puesto_en_curso }}</h3>
                    
                    <div class="relative px-20 py-10 bg-slate-900 border-2 border-indigo-500/30 rounded-[2.5rem] shadow-[0_0_80px_rgba(99,102,241,0.2)] mb-10 inline-block">
                        <span class="absolute -top-4 left-1/2 -translate-x-1/2 px-6 py-1 rounded-full bg-indigo-600 text-white font-black text-xs uppercase tracking-widest border border-indigo-500/30">
                            PIN DE ACCESO
                        </span>
                        <h1 class="text-8xl md:text-[8rem] font-extrabold tracking-[0.15em] text-indigo-400 select-all leading-none font-mono">
                            {{ $eleccionActiva->pin_ronda }}
                        </h1>
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-center gap-8 text-xl text-slate-350 font-bold bg-slate-900/50 p-6 rounded-2xl border border-slate-800 max-w-2xl mx-auto">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-black shadow-md">1</span>
                            <span>Ingresa al Portal del Votante</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-600 text-sm hidden md:inline"></i>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-black shadow-md">2</span>
                            <span>Usa el PIN para votar</span>
                        </div>
                    </div>
                @else
                    <div class="bg-slate-900/50 p-8 rounded-3xl border border-slate-800 text-center max-w-xl mx-auto">
                        <i class="fa-solid fa-hourglass-start text-5xl text-indigo-500/60 mb-4 animate-pulse"></i>
                        <h3 class="text-2xl font-bold text-white mb-2">Esperando Apertura de Ronda</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">El administrador está preparando la siguiente votación. Los resultados y el PIN de acceso aparecerán automáticamente aquí.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
    {{-- OVERLAY: PROYECCIÓN DEL PIN EN PANTALLA COMPLETA (DISEÑO PARA CAÑONERA) --}}
    <div id="proyectorPin" x-show="proyeccionPinActiva" x-cloak
         class="fixed inset-0 z-[10000] select-none overflow-hidden"
         style="background: radial-gradient(ellipse at 50% 40%, #0e0525 0%, #030711 60%, #000000 100%);">

        {{-- Grid de puntos decorativo de fondo --}}
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(#a78bfa 1px, transparent 1px); background-size: 40px 40px;"></div>

        {{-- Botón cerrar (esquina) --}}
        <button @click="salirProyeccion()" class="absolute top-5 right-5 z-10 px-4 py-2 bg-white/5 hover:bg-white/10 text-white/60 hover:text-white rounded-xl font-bold text-xs transition-all flex items-center gap-2 border border-white/10">
            <i class="fa-solid fa-compress text-[10px]"></i> Cerrar
        </button>

        {{-- BARRA SUPERIOR: Nombre de la iglesia --}}
        <div class="absolute top-0 left-0 right-0 px-12 py-5 flex items-center justify-between border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-600/30 border border-violet-500/40 flex items-center justify-center">
                    <i class="fa-solid fa-church text-violet-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-white/90 font-black text-sm tracking-wide">AD Rey de Reyes</p>
                    <p class="text-violet-400/70 text-[10px] font-bold uppercase tracking-[0.2em]">Sistema de Votación Electoral</p>
                </div>
            </div>
            @if(isset($eleccionActiva) && $eleccionActiva)
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-emerald-400 font-black text-[11px] uppercase tracking-widest">Votación en Curso</span>
                </div>
            @endif
        </div>

        {{-- CONTENIDO CENTRAL --}}
        <div class="flex flex-col items-center justify-center h-full pt-20 pb-32 px-8">

            @if(isset($eleccionActiva) && $eleccionActiva)
                <h3 class="text-violet-300 font-black text-xl md:text-2xl tracking-widest uppercase mb-2" style="color: #c4b5fd !important; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                    {{ $eleccionActiva->titulo }}
                </h3>
                @if($eleccionActiva->puesto_en_curso)
                    <p class="text-white/60 text-lg font-bold uppercase tracking-[0.3em] mb-3" style="color: rgba(255,255,255,0.8) !important;">Ronda abierta — cargo de</p>
                    <h2 class="font-black uppercase tracking-tight mb-10 leading-none text-white"
                        style="font-size: clamp(2.5rem, 6vw, 5rem); color: #ffffff !important; text-shadow: 0 0 40px rgba(167,139,250,0.8) !important;">
                        {{ $eleccionActiva->puesto_en_curso }}
                    </h2>
                @endif
            @endif

            {{-- Contenedor del PIN --}}
            <div class="relative flex flex-col items-center">
                {{-- Anillo de pulso exterior --}}
                <div class="absolute inset-[-40px] rounded-[4rem] border border-violet-500/20 animate-ping" style="animation-duration: 3s;"></div>
                <div class="absolute inset-[-20px] rounded-[3rem] border border-violet-500/15"></div>

                {{-- Tarjeta del PIN --}}
                <div class="relative px-20 py-12 rounded-[2.5rem] border-2 flex flex-col items-center"
                     style="background: linear-gradient(135deg, #1a0b3d 0%, #0d0a2a 50%, #080618 100%); border-color: rgba(139,92,246,0.5); box-shadow: 0 0 80px rgba(139,92,246,0.25), 0 0 160px rgba(139,92,246,0.1), inset 0 1px 0 rgba(255,255,255,0.05);">

                    <span class="text-[11px] font-black text-violet-400/80 uppercase tracking-[0.5em] mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-key"></i> PIN DE ACCESO
                    </span>

                    {{-- EL PIN GIGANTE --}}
                    <div class="flex items-center gap-4 font-mono font-black leading-none"
                         style="font-size: clamp(5rem, 18vw, 14rem); color: #ede9fe; letter-spacing: 0.15em; text-shadow: 0 0 40px rgba(196,181,253,0.8), 0 0 80px rgba(167,139,250,0.5), 0 0 120px rgba(139,92,246,0.3);">
                        @if(isset($eleccionActiva) && $eleccionActiva && $eleccionActiva->pin_ronda)
                            {{ $eleccionActiva->pin_ronda }}
                        @else
                            <span class="text-white/20">— — — —</span>
                        @endif
                    </div>

                    <span class="text-violet-400/50 text-xs font-bold uppercase tracking-[0.3em] mt-6">Ingresa este código en tu celular</span>
                </div>
            </div>
        </div>

        {{-- BARRA INFERIOR: Instrucciones paso a paso --}}
        <div class="absolute bottom-0 left-0 right-0 px-12 py-6 border-t border-white/5"
             style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
            <div class="flex items-center justify-center gap-6 max-w-3xl mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-600/30 border border-violet-500/40 flex items-center justify-center text-violet-300 font-black text-base">1</div>
                    <div>
                        <p class="text-white font-bold text-sm">Abre el Portal del Votante</p>
                        <p class="text-white/40 text-[11px]">Desde tu celular o tablet</p>
                    </div>
                </div>
                <div class="flex-1 h-px bg-white/10 max-w-16"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-600/30 border border-violet-500/40 flex items-center justify-center text-violet-300 font-black text-base">2</div>
                    <div>
                        <p class="text-white font-bold text-sm">Ingresa el PIN mostrado</p>
                        <p class="text-white/40 text-[11px]">Para acceder a la papeleta</p>
                    </div>
                </div>
                <div class="flex-1 h-px bg-white/10 max-w-16"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-600/30 border border-emerald-500/40 flex items-center justify-center text-emerald-300 font-black text-base">3</div>
                    <div>
                        <p class="text-white font-bold text-sm">Marca tu voto y confirma</p>
                        <p class="text-white/40 text-[11px]">Tu voto es secreto y seguro</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none !important;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none !important;  /* IE and Edge */
        scrollbar-width: none !important;  /* Firefox */
    }

    /* Custom Scrollbar Premium */
    .custom-scrollbar::-webkit-scrollbar { 
        width: 6px; 
        height: 6px; 
    }
    .custom-scrollbar::-webkit-scrollbar-track { 
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb { 
        background-color: rgba(99, 102, 241, 0.15); 
        border-radius: 10px; 
        transition: background-color 0.2s ease;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
        background-color: rgba(99, 102, 241, 0.35); 
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb,
    [data-theme='dark'] .custom-scrollbar::-webkit-scrollbar-thumb { 
        background-color: rgba(255, 255, 255, 0.08); 
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
    [data-theme='dark'] .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
        background-color: rgba(255, 255, 255, 0.18); 
    }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('organizacionesModule', (eleccionIdInicial, esAdmin, csrfToken, fechaFin, organizacionId) => ({
        procesando: false,
        esAdmin: esAdmin,
        eleccionId: eleccionIdInicial,
        organizacionId: organizacionId,
        candidatoSeleccionado: null,
        modalidad: 'autoservicio',
        puestoSeleccionado: '',
        proyeccionResultadosActiva: false,
        proyeccionPinActiva: false,
        mostrarModalManuales: false,
        votantesManuales: [],
        votosCandidatos: {},

        // Modales
        mostrarModalConfirmacion: false,
        mostrarModalPadron: false,
        mostrarModalCandidatos: false,
        mostrarModalDetener: false,
        mostrarModalCerrarRonda: false,

        // Toast
        toastVisible: false,
        toastMensaje: '',
        toastTipo: 'success',

        // Padrón
        padronSeleccionado: {!! isset($padronMiembros) ? $padronMiembros->pluck('id')->toJson() : '[]' !!},
        busquedaPadron: '',
        filtroOrgPadron: '',
        busquedaMiembrosDashboard: '',
        busquedaVotanteManual: '',

        // Candidatos
        busquedaCandidatos: '',
        filtroOrgCandidatos: '',
        candidatosSeleccionados: {!! $candidatos->map(fn($c) => ['miembro_id' => $c->miembro_id, 'puesto_postulado' => $c->puesto_postulado])->toJson() !!},

        // Iniciar elección
        nuevaEleccionTitulo: '',
        nuevaEleccionHoras: '8',
        nuevaEleccionTipoMayoria: 'simple',

        // Temporizador
        tiempoRestante: { horas: '00', minutos: '00', segundos: '00' },
        temporizadorInterval: null,

        init() {
            if (fechaFin) this.iniciarTemporizador(fechaFin);
            
            const handleFullscreenChange = () => {
                this.proyeccionResultadosActiva = !!(
                    document.fullscreenElement ||
                    document.webkitFullscreenElement ||
                    document.mozFullScreenElement ||
                    document.msFullscreenElement
                );
                if (!this.proyeccionResultadosActiva) this.proyeccionPinActiva = false;
            };
            document.addEventListener('fullscreenchange', handleFullscreenChange);
            document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
            document.addEventListener('mozfullscreenchange', handleFullscreenChange);
            document.addEventListener('MSFullscreenChange', handleFullscreenChange);
        },

        iniciarTemporizador(fechaLimite) {
            const fin = new Date(fechaLimite).getTime();
            this.temporizadorInterval = setInterval(() => {
                const distancia = fin - new Date().getTime();
                if (distancia < 0) {
                    clearInterval(this.temporizadorInterval);
                    this.tiempoRestante = { horas: '00', minutos: '00', segundos: '00' };
                    return;
                }
                this.tiempoRestante.horas = String(Math.floor((distancia % (864e5)) / 36e5)).padStart(2,'0');
                this.tiempoRestante.minutos = String(Math.floor((distancia % 36e5) / 6e4)).padStart(2,'0');
                this.tiempoRestante.segundos = String(Math.floor((distancia % 6e4) / 1000)).padStart(2,'0');
            }, 1000);
        },

        removerAcentos(texto) {
            if (!texto) return '';
            return texto.toString().normalize("NFD").replace(/[\u0300-\u036f]/g,"").toLowerCase();
        },

        mostrarNotificacion(mensaje, tipo) {
            this.toastMensaje = mensaje; this.toastTipo = tipo; this.toastVisible = true;
            setTimeout(() => this.toastVisible = false, 5000);
        },

        solicitarConfirmacion() {
            this.mostrarModalConfirmacion = true;
        },

        async peticionSegura(url, method, bodyData) {
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(bodyData)
            });
            const text = await response.text();
            let result;
            try { result = JSON.parse(text); } catch(e) { throw new Error('Error 500 en el servidor.'); }
            if (!response.ok) {
                const msg = result.message
                    || (result.errors ? Object.values(result.errors)[0]?.[0] : null)
                    || `Error ${response.status}`;
                throw new Error(msg);
            }
            return result;
        },

        async iniciarEleccion() {
            if (!this.nuevaEleccionTitulo.trim()) return;
            this.procesando = true;
            try {
                await this.peticionSegura(`/organizaciones/${this.organizacionId}/iniciar-eleccion`, 'POST', {
                    titulo: this.nuevaEleccionTitulo,
                    duracion_horas: parseInt(this.nuevaEleccionHoras),
                    tipo_mayoria: this.nuevaEleccionTipoMayoria,
                });
                this.mostrarNotificacion('✅ Elección iniciada correctamente.', 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        async detenerEleccion() {
            this.mostrarModalDetener = false;
            this.procesando = true;
            try {
                await this.peticionSegura(`/elecciones/${this.eleccionId}/estado`, 'PATCH', { estado: 'finalizada' });
                this.mostrarNotificacion('🛑 Elección detenida correctamente.', 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        async guardarPadron() {
            this.procesando = true;
            try {
                await this.peticionSegura(`/organizaciones/${this.organizacionId}/sync-miembros`, 'POST', { miembros: this.padronSeleccionado });
                this.mostrarNotificacion('✅ Padrón actualizado correctamente.', 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        toggleCandidato(miembroId) {
            const idx = this.candidatosSeleccionados.findIndex(c => c.miembro_id === miembroId);
            if (idx > -1) this.candidatosSeleccionados.splice(idx, 1);
            else this.candidatosSeleccionados.push({ miembro_id: miembroId, puesto_postulado: 'Presidente' });
        },
        updateCandidatoPost(miembroId, puesto) {
            const c = this.candidatosSeleccionados.find(c => c.miembro_id === miembroId);
            if (c) c.puesto_postulado = puesto;
        },
        isCandidatoChecked(miembroId) { return this.candidatosSeleccionados.some(c => c.miembro_id === miembroId); },
        getCandidatoPost(miembroId) {
            const c = this.candidatosSeleccionados.find(c => c.miembro_id === miembroId);
            return c ? c.puesto_postulado : 'Presidente';
        },

        async guardarCandidatos() {
            this.procesando = true;
            try {
                await this.peticionSegura(`/elecciones/${this.eleccionId}/sync-candidatos`, 'POST', { candidatos: this.candidatosSeleccionados });
                this.mostrarNotificacion('✅ Candidatos actualizados.', 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        async emitirVoto() {
            this.mostrarModalConfirmacion = false;
            this.procesando = true;
            try {
                const metaUser = document.head.querySelector('meta[name="user-miembro-id"]');
                let miembroId = metaUser ? metaUser.content : null;
                await this.peticionSegura('/votos/emitir', 'POST', {
                    eleccion_id: this.eleccionId,
                    candidato_id: this.candidatoSeleccionado,
                    miembro_id: miembroId,
                    modalidad: 'autoservicio'
                });
                this.mostrarNotificacion('✅ Voto registrado exitosamente.', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        async abrirRonda() {
            if (this.procesando || !this.puestoSeleccionado) return;
            this.procesando = true;
            try {
                await this.peticionSegura(`/elecciones/${this.eleccionId}/ronda/abrir`, 'PATCH', { puesto: this.puestoSeleccionado });
                window.location.reload();
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        confirmarCerrarRonda() {
            if (this.procesando) return;
            this.mostrarModalCerrarRonda = true;
        },

        async cerrarRonda() {
            this.mostrarModalCerrarRonda = false;
            this.procesando = true;
            try {
                await this.peticionSegura(`/elecciones/${this.eleccionId}/ronda/cerrar`, 'PATCH', {});
                window.location.reload();
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        },

        async guardarVotosManuales() {
            const totalVotos = Object.values(this.votosCandidatos).reduce((a, b) => parseInt(a || 0) + parseInt(b || 0), 0);
            if (totalVotos > this.votantesManuales.length) {
                this.mostrarNotificacion('Error: Hay mas votos asignados que balotas marcadas.', 'error');
                return;
            }
            if (!confirm('¿Seguro? Esta acción sumará estos votos permanentemente.')) return;

            this.procesando = true;
            try {
                await this.peticionSegura(`/elecciones/${this.eleccionId}/ronda/manuales`, 'POST', {
                    miembros: this.votantesManuales,
                    votos_candidatos: this.votosCandidatos
                });
                this.mostrarNotificacion('Balotas procesadas.', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } catch (error) {
                this.mostrarNotificacion('Error: ' + error.message, 'error');
                this.procesando = false;
            }
        },

        iniciarProyeccion() {
            this.proyeccionResultadosActiva = true;
            this.$nextTick(() => {
                const el = document.getElementById('proyectorOverlay');
                if (el) {
                    if (el.requestFullscreen) {
                        el.requestFullscreen();
                    } else if (el.webkitRequestFullscreen) {
                        el.webkitRequestFullscreen();
                    } else if (el.mozRequestFullScreen) {
                        el.mozRequestFullScreen();
                    } else if (el.msRequestFullscreen) {
                        el.msRequestFullscreen();
                    }
                }
            });
        },

        salirProyeccion() {
            this.proyeccionResultadosActiva = false;
            this.proyeccionPinActiva = false;
            if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        },

        proyectarPin() {
            // Lanza el overlay de proyección mostrando solo el PIN en pantalla completa
            this.proyeccionPinActiva = true;
            this.$nextTick(() => {
                const el = document.getElementById('proyectorPin');
                if (el) {
                    if (el.requestFullscreen) el.requestFullscreen();
                    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                    else if (el.mozRequestFullScreen) el.mozRequestFullScreen();
                    else if (el.msRequestFullscreen) el.msRequestFullscreen();
                }
            });
        },

        async generarNuevoPin() {
            if (this.procesando) return;
            if (!confirm('¿Generar un nuevo PIN de acceso? El PIN anterior quedará inválido de inmediato.')) return;
            this.procesando = true;
            try {
                await this.peticionSegura(`/elecciones/${this.eleccionId}/ronda/regenerar-pin`, 'PATCH', {});
                this.mostrarNotificacion('🔑 Nuevo PIN generado. Recargando...', 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                this.mostrarNotificacion('❌ ' + error.message, 'error');
                this.procesando = false;
            }
        }
    }));
});
</script>
@endsection
