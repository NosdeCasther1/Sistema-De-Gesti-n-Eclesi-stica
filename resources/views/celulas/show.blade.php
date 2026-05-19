@extends('layouts.app')

@section('title', 'Detalle de Célula - AD Rey de Reyes')

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
    .icon-box-celulas { background: linear-gradient(135deg, #2563eb, #4f46e5) !important; color: white !important; }

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

@section('header_title', 'Detalle de Célula')
@section('header_subtitle', 'Visualización de integrantes, horarios y controles de asistencia')
@section('header_icon')
<i class="fas fa-network-wired fs-5"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-7xl mx-auto">
    <!-- Barra de Navegación / Regreso -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1 flex items-center gap-3">
                <span>{{ $celula->nombre }}</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 uppercase tracking-wider">
                    <i class="fas fa-users text-xs"></i> {{ $celula->miembros->count() }} {{ $celula->miembros->count() == 1 ? 'Integrante' : 'Integrantes' }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Revisión general de horarios, líder a cargo y lista oficial de integrantes</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('asistencia.scanner', ['celula_id' => $celula->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md transition-all no-underline">
                <i class="fas fa-qrcode text-sm"></i>
                <span>Asistencia QR</span>
            </a>
            <a href="{{ route('asistencia.manual', ['celula_id' => $celula->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md transition-all no-underline">
                <i class="fas fa-edit text-sm"></i>
                <span>Toma Manual</span>
            </a>
            <a href="{{ route('reportes.asistencia_celula', $celula->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-teal-500 hover:bg-teal-600 text-white shadow-md transition-all no-underline">
                <i class="fas fa-file-invoice text-sm"></i>
                <span>Reporte Mensual</span>
            </a>
            <a href="{{ route('celulas.edit', $celula->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700/60 shadow-sm transition-all no-underline">
                <i class="fas fa-edit text-sm"></i>
                <span>Editar</span>
            </a>
            <a href="{{ route('celulas.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/80 shadow-sm transition-all no-underline">
                <i class="fas fa-arrow-left text-sm"></i>
                <span>Volver</span>
            </a>
        </div>
    </div>

    <!-- Grid Bento Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==========================================
             COLUMNA IZQUIERDA (5 COLUMNAS): INFORMACIÓN DE LA CÉLULA
        ========================================== -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
                <!-- Glow de fondo -->
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

                <!-- Header Expediente -->
                <div class="flex items-center gap-4 mb-8 pb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="report-icon-box icon-box-celulas group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-1">Información General</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Líder asignado, horarios y sectorización</p>
                    </div>
                </div>

                <!-- Detalles de la Célula -->
                <div class="space-y-6">
                    <!-- Líder Responsable -->
                    <div class="p-5 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-user text-blue-500"></i> Líder Responsable
                        </span>
                        <span class="text-sm font-bold text-slate-950 dark:text-slate-200 leading-relaxed">
                            {{ $celula->lider->nombres ?? 'Sin Líder' }} {{ $celula->lider->apellidos ?? '' }}
                        </span>
                        @if($celula->lider)
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                <i class="fas fa-phone-alt"></i> {{ $celula->lider->telefono ?? 'Sin teléfono registrado' }}
                            </span>
                        @endif
                    </div>

                    <!-- Horario de Reunión -->
                    <div class="p-5 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5 flex items-center gap-1.5">
                            <i class="far fa-clock text-emerald-500"></i> Día y Hora de Reunión
                        </span>
                        <span class="text-sm font-bold text-slate-950 dark:text-slate-200 tracking-tight">
                            {{ $celula->dia_reunion }} a las {{ \Carbon\Carbon::parse($celula->hora_reunion)->format('h:i A') }}
                        </span>
                    </div>

                    <!-- Ubicación de Célula -->
                    <div class="p-5 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner flex flex-col justify-center">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-rose-500"></i> Ubicación / Dirección
                        </span>
                        <span class="text-sm font-bold text-slate-950 dark:text-slate-200 mb-2 leading-relaxed">
                            {{ $celula->direccion ?? 'Sin dirección registrada' }}
                        </span>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 uppercase tracking-wider">
                                {{ $celula->sector ?? 'Sin Sector' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             COLUMNA DERECHA (7 COLUMNAS): INTEGRANTES
        ========================================== -->
        <div class="lg:col-span-7 flex flex-col">
            <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
                <!-- Glow de fondo -->
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>

                <!-- Header Integrantes -->
                <div class="flex items-center gap-4 mb-8 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap justify-between">
                    <div class="flex items-center gap-4">
                        <div class="report-icon-box icon-box-celulas group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-users-viewfinder"></i>
                        </div>
                        <div>
                            <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-1">Integrantes de la Célula</h5>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Lista de miembros consolidados en este grupo de pastoreo</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Integrantes -->
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-table-scroll">
                    @if($celula->miembros->count() > 0)
                        @foreach($celula->miembros as $miembro)
                            <a href="{{ route('miembros.show', $miembro->id) }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 transition-all shadow-sm group/item no-underline">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 relative flex-shrink-0">
                                        <img src="{{ $miembro->foto ? asset('storage/miembros/' . $miembro->foto) : asset('assets/img/default_avatar.png') }}" 
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($miembro->nombres . ' ' . $miembro->apellidos) }}&background=0D8abc&color=fff&size=200'"
                                             class="w-full h-full rounded-xl object-cover border-2 border-white dark:border-slate-800 shadow-sm">
                                    </div>
                                    <div>
                                        <h6 class="text-sm font-bold text-slate-900 dark:text-white mb-0.5 tracking-tight group-hover/item:text-blue-600 dark:group-hover/item:text-blue-400 transition-colors">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h6>
                                        <div class="flex items-center gap-3 text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                                            <span class="flex items-center gap-1"><i class="fas fa-phone"></i> {{ $miembro->telefono ?? 'Sin teléfono' }}</span>
                                            <span class="flex items-center gap-1"><i class="fas fa-church"></i> {{ $miembro->ministerio ?? 'Sin ministerio' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 uppercase tracking-wider">
                                        {{ $miembro->etapa_consolidacion }}
                                    </span>
                                    <i class="fas fa-chevron-right text-slate-400 group-hover/item:translate-x-1 transition-transform"></i>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-12 bg-slate-50/50 dark:bg-slate-800/20 rounded-3xl border border-slate-100 dark:border-slate-800/80">
                            <i class="fas fa-user-slash text-slate-400 dark:text-slate-600 text-4xl mb-3 opacity-50"></i>
                            <h6 class="text-sm font-bold text-slate-800 dark:text-slate-300 mb-1">Sin integrantes asignados</h6>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Asigne miembros a esta célula desde el perfil del miembro o editándolos.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Expediente -->
                <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center justify-between">
                    <span>ID de Célula: #{{ str_pad($celula->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span>AD Rey de Reyes</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
