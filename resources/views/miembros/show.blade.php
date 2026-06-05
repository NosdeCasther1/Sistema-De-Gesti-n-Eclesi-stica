@extends('layouts.app')

@section('title', 'Perfil del Miembro: ' . $miembro->nombres . ' ' . $miembro->apellidos . ' - AD Rey de Reyes')

@push('styles')
<style>
    /* Bento Card Hover Effects */
    .bento-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bento-card:hover {
        transform: translateY(-4px);
    }

    /* Bento Buttons Premium (Bulletproof Gradients) */
    .btn-bento-membresia {
        background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(37,99,235,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-membresia:hover {
        background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
        box-shadow: 0 6px 20px rgba(37,99,235,0.35) !important;
        transform: translateY(-2px);
    }

    /* Icon Boxes Premium */
    .report-icon-box {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        border-radius: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2) !important;
    }
    .report-icon-box i {
        color: white !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-block !important;
        font-size: 1.4rem !important;
    }
    .icon-box-membresia { background: linear-gradient(135deg, #2563eb, #4f46e5) !important; color: white !important; }
</style>
@endpush

@section('header_title', 'Perfil del Miembro')
@section('header_subtitle', 'Expediente digital, información ministerial y credenciales oficiales')
@section('header_icon')
<i class="fas fa-user-check fs-5"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-7xl mx-auto">
    <!-- Barra de Navegación / Regreso -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1 flex items-center gap-3">
                <span>{{ $miembro->nombres }} {{ $miembro->apellidos }}</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 uppercase tracking-wider">
                    <i class="fas fa-award text-xs"></i> {{ $miembro->etapa_consolidacion }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Revisión general de expediente y generación de carnet de identificación</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('miembros.carta_recomendacion', $miembro->id) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fa-solid fa-file-signature text-indigo-500"></i> Recomendación
            </a>
            <a href="{{ route('miembros.carta_traslado', $miembro->id) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fa-solid fa-paper-plane text-emerald-500"></i> Traslado
            </a>
            <a href="{{ route('miembros.index') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-sm transition-all no-underline">
                <i class="fas fa-arrow-left text-sm"></i>
                <span>Volver al listado</span>
            </a>
        </div>
    </div>

    <!-- Grid Bento Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==========================================
             COLUMNA IZQUIERDA (4 COLUMNAS): TARJETA DE IDENTIDAD
        ========================================== -->
        <div class="lg:col-span-4 flex flex-col">
            <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl text-center relative overflow-hidden group">
                <!-- Glow de fondo -->
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

                <!-- Avatar Premium -->
                <div class="w-40 h-40 mx-auto mb-6 relative group/avatar">
                    <img src="{{ $miembro->foto ? asset('storage/miembros/' . $miembro->foto) : asset('assets/img/default_avatar.png') }}" 
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($miembro->nombres . ' ' . $miembro->apellidos) }}&background=0D8abc&color=fff&size=200'"
                         class="w-full h-full rounded-3xl object-cover border-4 border-white dark:border-slate-800 shadow-2xl group-hover/avatar:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 rounded-3xl ring-1 ring-inset ring-slate-900/10 dark:ring-white/10 pointer-events-none"></div>
                </div>

                <!-- Nombres y Ministerio -->
                <h4 class="text-xl font-black text-slate-900 dark:text-white mb-1 tracking-tight">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h4>
                <div class="mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-sm">
                        <i class="fas fa-church text-xs"></i> 
                        @if($miembro->ministerios->isNotEmpty())
                            {{ $miembro->es_lider ? 'Líder - ' : '' }}{{ $miembro->ministerios->pluck('nombre')->implode(', ') }}
                        @else
                            {{ $miembro->es_lider ? 'Líder' : 'Sin Ministerio Asignado' }}
                        @endif
                    </span>
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-col gap-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                    <a href="{{ route('miembros.carnet', $miembro->id) }}" target="_blank" class="btn-bento-membresia w-full py-3.5 px-5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2.5 shadow-lg hover:shadow-blue-500/25 transition-all no-underline cursor-pointer">
                        <i class="fas fa-id-card text-base"></i>
                        <span>Descargar Carnet Oficial</span>
                    </a>
                    @if($miembro->etapa_consolidacion == 'Bautizado' || $miembro->fecha_bautismo)
                    <a href="{{ route('miembros.certificado_bautismo', $miembro->id) }}" target="_blank" class="w-full py-3.5 px-5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2 bg-yellow-50 dark:bg-yellow-500/10 hover:bg-yellow-100 dark:hover:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-500/30 transition-all no-underline shadow-sm cursor-pointer">
                        <i class="fa-solid fa-certificate text-yellow-600 dark:text-yellow-500 text-base"></i>
                        <span>Certificado de Bautismo</span>
                    </a>
                    @endif
                    <a href="{{ route('miembros.edit', $miembro->id) }}" class="w-full py-3.5 px-5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 transition-all no-underline shadow-sm cursor-pointer">
                        <i class="fas fa-user-edit text-base"></i>
                        <span>Editar Datos del Miembro</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ==========================================
             COLUMNA DERECHA (8 COLUMNAS): EXPEDIENTE DETALLADO
        ========================================== -->
        <div class="lg:col-span-8 flex flex-col">
            <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
                <!-- Glow de fondo -->
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>

                <!-- Header Expediente -->
                <div class="flex items-center gap-4 mb-8 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap justify-between">
                    <div class="flex items-center gap-4">
                        <div class="report-icon-box icon-box-membresia group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Información Personal y Contacto</h5>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Datos demográficos, ubicación y registro de consolidación</p>
                        </div>
                    </div>
                </div>

                <!-- Grid de Datos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- DPI -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-id-card text-blue-500"></i> DPI / Identidad
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->dpi ?? 'N/A' }}</span>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-emerald-500"></i> Fecha de Nacimiento
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->fecha_nacimiento ? $miembro->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</span>
                    </div>

                    <!-- Sexo -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-venus-mars text-indigo-500"></i> Sexo
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->sexo === 'M' ? 'Masculino' : ($miembro->sexo === 'F' ? 'Femenino' : 'N/A') }}</span>
                    </div>

                    <!-- Estado Civil -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-ring text-amber-500"></i> Estado Civil
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->estado_civil ?? 'N/A' }}</span>
                    </div>

                    <!-- Cónyuge -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-heart text-rose-500"></i> Cónyuge
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">
                            @if($miembro->conyuge)
                                <a href="{{ route('miembros.show', $miembro->conyuge->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $miembro->conyuge->nombres }} {{ $miembro->conyuge->apellidos }}
                                </a>
                            @else
                                N/A
                            @endif
                        </span>
                    </div>

                    <!-- Teléfono -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-phone text-emerald-500"></i> Teléfono
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->telefono ?? 'Sin teléfono' }}</span>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-envelope text-amber-500"></i> Correo Electrónico
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->email ?? 'Sin correo' }}</span>
                    </div>

                    <!-- Familia -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-people-roof text-rose-500"></i> Familia Asignada
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">
                            @if($miembro->familia)
                                <a href="{{ route('familias.show', $miembro->familia->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $miembro->familia->nombre }} (ID: #{{ str_pad($miembro->familia->id, 5, '0', STR_PAD_LEFT) }})
                                </a>
                            @else
                                Sin familia asignada
                            @endif
                        </span>
                    </div>

                    <!-- Lugar y Fecha de Conversión -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center md:col-span-2">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-heart text-rose-500"></i> Conversión
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $miembro->lugar_conversion ?? 'Lugar no especificado' }} 
                            @if($miembro->fecha_conversion)
                            - {{ $miembro->fecha_conversion->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>

                    <!-- Fecha de Integración -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-calendar-check text-cyan-500"></i> Fecha de Integración
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->fecha_integracion ? $miembro->fecha_integracion->format('d/m/Y') : 'N/A' }}</span>
                    </div>

                    <!-- Bautismo en Aguas -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-water text-blue-500"></i> Bautizado en Aguas
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">
                            @if($miembro->bautizado_agua || $miembro->fecha_bautismo)
                                Sí @if($miembro->fecha_bautismo) ({{ $miembro->fecha_bautismo->format('d/m/Y') }}) @endif
                            @else
                                No
                            @endif
                        </span>
                    </div>

                    <!-- Bautismo en el Espíritu Santo -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-fire text-amber-500"></i> Bautismo Espíritu Santo
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $miembro->bautismo_espiritu_santo ? 'Sí' : 'No' }}
                        </span>
                    </div>

                    <!-- Dirección Residencial -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center md:col-span-2">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-map-location-dot text-rose-500"></i> Dirección Residencial
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ collect([$miembro->direccion, $miembro->zona, $miembro->municipio, $miembro->departamento])->filter()->join(', ') ?: 'N/A' }}</span>
                    </div>

                    <!-- Nivel Académico -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-graduation-cap text-blue-500"></i> Nivel Académico
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->nivel_academico ?? 'N/A' }}</span>
                    </div>

                    <!-- Profesión / Oficio -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-briefcase text-amber-500"></i> Profesión / Oficio
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->profesion ?? 'N/A' }}</span>
                    </div>

                    <!-- Lugar de Trabajo/Estudio -->
                    <div class="p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center md:col-span-2">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-building text-indigo-500"></i> Lugar de Trabajo / Estudio
                        </span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $miembro->lugar_trabajo_estudio ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Récords Disciplinarios -->
                <div class="mt-8" x-data="{ openRecord: false }">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <div class="report-icon-box icon-box-membresia group-hover:scale-110 transition-transform duration-500 bg-rose-50 dark:bg-rose-500/10 text-rose-500 border-rose-100 dark:border-rose-500/20">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <div>
                                <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Récords y Faltas</h5>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Historial de faltas y acciones tomadas</p>
                            </div>
                        </div>
                        @if(auth()->user() && auth()->user()->hasRole('administrador'))
                            <button @click="openRecord = true" class="px-4 py-2 bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 rounded-xl font-bold hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors text-xs flex items-center gap-2">
                                <i class="fas fa-plus"></i> Añadir Récord
                            </button>
                        @endif
                    </div>

                    @if($miembro->records->count() > 0)
                        <div class="space-y-4">
                            @foreach($miembro->records()->orderBy('fecha', 'desc')->get() as $record)
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $record->tipo_falta == 'Grave' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                                {{ $record->tipo_falta }}
                                            </span>
                                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($record->fecha)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-slate-800 dark:text-white font-medium mb-1">{{ $record->descripcion }}</p>
                                        @if($record->accion_tomada)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0"><strong>Acción Tomada:</strong> {{ $record->accion_tomada }}</p>
                                        @endif
                                    </div>
                                    @if(auth()->user() && auth()->user()->hasRole('administrador'))
                                        <form action="{{ route('miembros.records.destroy', $record) }}" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar este récord?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 rounded-2xl p-6 text-center">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-0">No hay récords registrados para este miembro.</p>
                        </div>
                    @endif

                    <!-- Modal Añadir Record -->
                    <div x-show="openRecord" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg p-6 shadow-2xl m-4 relative border border-slate-200 dark:border-slate-800" @click.outside="openRecord = false">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Añadir Nuevo Récord</h3>
                                <button type="button" @click="openRecord = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
                            </div>
                            <form action="{{ route('miembros.records.store', $miembro) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Fecha de la Falta *</label>
                                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Tipo de Falta *</label>
                                    <select name="tipo_falta" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20">
                                        <option value="Leve">Leve</option>
                                        <option value="Grave">Grave</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Descripción de la Falta *</label>
                                    <textarea name="descripcion" rows="3" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Acción Tomada (Opcional)</label>
                                    <textarea name="accion_tomada" rows="2" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20"></textarea>
                                </div>
                                <div class="pt-4 flex justify-end gap-2">
                                    <button type="button" @click="openRecord = false" class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-sm font-bold rounded-xl transition-colors">Guardar Récord</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Footer Expediente -->
                <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <span class="flex items-center gap-2"><i class="fas fa-shield-alt text-blue-500"></i> Registro oficial con auditoría inmutable de partida doble</span>
                    <span>ID de Expediente: #{{ str_pad($miembro->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
