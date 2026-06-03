@extends('layouts.app')

@section('title', 'Centro de Reportes - AD Rey de Reyes')

@section('header_title', 'Centro de Reportes')
@section('header_subtitle', 'Documentación oficial, censos y análisis ministerial de partida doble')
@section('header_icon')
<i class="fas fa-file-invoice-dollar fs-5"></i>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }

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

    .btn-bento-finanzas {
        background: linear-gradient(135deg, #059669, #0d9488) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(5,150,105,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-finanzas:hover {
        background: linear-gradient(135deg, #047857, #0f766e) !important;
        box-shadow: 0 6px 20px rgba(5,150,105,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-asistencia {
        background: linear-gradient(135deg, #0891b2, #2563eb) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(8,145,178,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-asistencia:hover {
        background: linear-gradient(135deg, #0e7490, #1d4ed8) !important;
        box-shadow: 0 6px 20px rgba(8,145,178,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-bautizados {
        background: linear-gradient(135deg, #d97706, #ea580c) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(217,119,6,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-bautizados:hover {
        background: linear-gradient(135deg, #b45309, #c2410c) !important;
        box-shadow: 0 6px 20px rgba(217,119,6,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-familia {
        background: linear-gradient(135deg, #e11d48, #dc2626) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(225,29,72,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-familia:hover {
        background: linear-gradient(135deg, #be123c, #b91c1c) !important;
        box-shadow: 0 6px 20px rgba(225,29,72,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-votaciones {
        background: linear-gradient(135deg, #6d0d0d, #c9a227) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(109,13,13,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-votaciones:hover {
        background: linear-gradient(135deg, #590a0a, #b08c20) !important;
        box-shadow: 0 6px 20px rgba(109,13,13,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-inventario {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(139,92,246,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-inventario:hover {
        background: linear-gradient(135deg, #7c3aed, #5b21b6) !important;
        box-shadow: 0 6px 20px rgba(139,92,246,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-organizaciones {
        background: linear-gradient(135deg, #db2777, #be185d) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(219,39,119,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-organizaciones:hover {
        background: linear-gradient(135deg, #be185d, #9d174d) !important;
        box-shadow: 0 6px 20px rgba(219,39,119,0.35) !important;
        transform: translateY(-2px);
    }

    /* Icon Boxes Premium (Bulletproof Gradients & Dimensions) */
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

    .icon-box-membresia { background: linear-gradient(135deg, #2563eb, #4f46e5) !important; color: white !important; }
    .icon-box-finanzas { background: linear-gradient(135deg, #059669, #10b981) !important; color: white !important; }
    .icon-box-asistencia { background: linear-gradient(135deg, #0891b2, #06b6d4) !important; color: white !important; }
    .icon-box-bautizados { background: linear-gradient(135deg, #d97706, #f59e0b) !important; color: white !important; }
    .icon-box-familia { background: linear-gradient(135deg, #e11d48, #f43f5e) !important; color: white !important; }
    .icon-box-votaciones { background: linear-gradient(135deg, #6d0d0d, #c9a227) !important; color: white !important; }
    .icon-box-inventario { background: linear-gradient(135deg, #8b5cf6, #a78bfa) !important; color: white !important; }
    .icon-box-organizaciones { background: linear-gradient(135deg, #db2777, #f472b6) !important; color: white !important; }
</style>
@endpush

@section('content')
<!-- Contenedor Principal Alpine.js -->
<div x-data="{ showTesoreriaModal: false, showInventarioModal: false, showMembresiaModal: false, showOrganizacionesModal: false }" class="container-fluid py-6 px-4 max-w-7xl mx-auto">

    <!-- ==========================================
         SECCIÓN DE ESTADÍSTICAS RÁPIDAS (BENTO DASHBOARD - AL PRINCIPIO)
    ========================================== -->
    <div class="mb-16 border-b border-slate-200 dark:border-slate-800/80 pb-12">
        <div class="flex items-center gap-3 mb-6">
            <div class="stat-icon-box bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-sm flex-shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h5 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight mb-0">Métricas y Resumen Ministerial</h5>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Indicadores clave de rendimiento correspondientes al mes de {{ $mesActual }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stat 1: Membresía Total -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 dark:bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
                <div class="stat-icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Membresía Total</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalMiembros }}</div>
                </div>
            </div>

            <!-- Stat 2: Células Activas -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-cyan-500/5 dark:bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/10 transition-all"></div>
                <div class="stat-icon-box bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-100 dark:border-cyan-500/20 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-house-chimney-user"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Células Activas</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalCelulas }}</div>
                </div>
            </div>

            <!-- Stat 3: Cajas Financieras -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/5 dark:bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
                <div class="stat-icon-box bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Cajas Financieras</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ count($accounts) }}</div>
                </div>
            </div>

            <!-- Stat 4: Disponibilidad Consolidada -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/5 dark:bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>
                <div class="stat-icon-box bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-scale-balanced"></i>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Fondo Consolidado</div>
                    <div class="text-xl font-black text-slate-900 dark:text-white tracking-tight truncate max-w-[150px]">Q{{ number_format($accounts->sum('initial_balance') + $accounts->sum('total_balance'), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Bento Principal -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- ==========================================
             1. REPORTE DE MEMBRESÍA (AZUL REY / ÍNDIGO)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

            <div>
                <!-- Header Card -->
                <div class="flex items-center gap-4 mb-5">
                    <div class="report-icon-box icon-box-membresia group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Membresía</h5>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20">
                            <i class="fas fa-file-pdf text-xs"></i> Censo Oficial
                        </span>
                    </div>
                </div>
                <!-- Descripción -->
                <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-6">
                    Genera el padrón y censo general de la congregación con datos completos de contacto, asignación ministerial y etapa de consolidación actual.
                </p>
            </div>

            <!-- Acción -->
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button type="button" @click="showMembresiaModal = true" class="btn-bento-membresia w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer">
                    <i class="fas fa-cloud-arrow-down text-base"></i>
                    <span>Descargar Censo General</span>
                </button>
            </div>
        </div>

        <!-- ==========================================
             1.5 REPORTE DE INVENTARIO (VIOLETA / PÚRPURA)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-violet-500/10 dark:bg-violet-500/5 rounded-full blur-3xl group-hover:bg-violet-500/20 transition-all duration-500"></div>

            <div>
                <!-- Header Card -->
                <div class="flex items-center gap-4 mb-5">
                    <div class="report-icon-box icon-box-inventario group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Inventario</h5>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-100 dark:border-violet-500/20">
                            <i class="fas fa-file-pdf text-xs"></i> Activos
                        </span>
                    </div>
                </div>
                <!-- Descripción -->
                <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-6">
                    Genera un reporte completo de los artículos, equipos y activos de la iglesia, detallando su ubicación, estado actual y responsables asignados.
                </p>
            </div>

            <!-- Acción -->
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button type="button" @click="showInventarioModal = true" class="btn-bento-inventario w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer">
                    <i class="fas fa-cloud-arrow-down text-base"></i>
                    <span>Descargar Inventario</span>
                </button>
            </div>
        </div>

        <!-- ==========================================
             2. REPORTE DE FINANZAS (ESMERALDA / TEAL)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>

            <div>
                <!-- Header Card -->
                <div class="flex items-center gap-4 mb-5">
                    <div class="report-icon-box icon-box-finanzas group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-vault"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Finanzas y Tesorería</h5>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                            <i class="fas fa-shield-check text-xs"></i> Corte de Caja
                        </span>
                    </div>
                </div>
                <!-- Descripción -->
                <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-6">
                    Corte de caja mensual y por período con balance de ingresos, egresos y auditoría inmutable desglosada por partidas (Diezmos, Ofrendas, Misiones y Gastos).
                </p>
            </div>

            <!-- Acción -->
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button type="button" @click="showTesoreriaModal = true" class="btn-bento-finanzas w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer">
                    <i class="fas fa-sliders text-base"></i>
                    <span>Configurar y Generar Corte</span>
                </button>
            </div>
        </div>

        <!-- ==========================================
             3. REPORTE DE ORGANIZACIONES (ROSA / MAGENTA)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-pink-500/10 dark:bg-pink-500/5 rounded-full blur-3xl group-hover:bg-pink-500/20 transition-all duration-500"></div>

            <div>
                <!-- Header Card -->
                <div class="flex items-center gap-4 mb-5">
                    <div class="report-icon-box icon-box-organizaciones group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Organizaciones</h5>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-pink-50 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400 border border-pink-100 dark:border-pink-500/20">
                            <i class="fas fa-users-rectangle text-xs"></i> Estructura
                        </span>
                    </div>
                </div>
                <!-- Descripción -->
                <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-6">
                    Genera el reporte oficial de las organizaciones activas en la iglesia, visualizando su estructura, directivas y listado completo de integrantes asignados.
                </p>
            </div>

            <!-- Acción -->
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button type="button" @click="showOrganizacionesModal = true" class="btn-bento-organizaciones w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer">
                    <i class="fas fa-file-pdf text-base"></i>
                    <span>Descargar Estructura</span>
                </button>
            </div>
        </div>

        <!-- ==========================================
             4. REPORTES DE ASISTENCIA (CIAN / AZUL - 2 COLUMNAS INTERNAS)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group md:col-span-2 lg:col-span-2">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-cyan-500/10 dark:bg-cyan-500/5 rounded-full blur-3xl group-hover:bg-cyan-500/20 transition-all duration-500"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center my-auto">
                <!-- Columna Izquierda: Información y Descripción -->
                <div>
                    <!-- Header Card -->
                    <div class="flex items-center gap-4 mb-5">
                        <div class="report-icon-box icon-box-asistencia group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Control de Asistencia</h5>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-100 dark:border-cyan-500/20">
                                <i class="fas fa-qrcode text-xs"></i> Mapeo Matricial
                            </span>
                        </div>
                    </div>
                    <!-- Descripción -->
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-0">
                        Genera constancias de asistencia matricial por célula familiar o el listado solemne de asistentes a un evento o servicio general. Esta herramienta permite un seguimiento riguroso y automatizado para fortalecer la consolidación y el cuidado pastoral de cada miembro.
                    </p>
                </div>

                <!-- Columna Derecha: Formulario Dinámico de Selección -->
                <div class="space-y-3.5 bg-slate-50/60 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-inner">
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 block">Modalidad de Reporte</label>
                        <select class="w-full rounded-xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-3.5 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 dark:focus:border-cyan-500 transition-all shadow-sm" id="select-tipo-asistencia-reporte" onchange="toggleAsistenciaReportType()">
                            <option value="celula" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Asistencia por Célula Familiar</option>
                            <option value="evento" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Asistencia por Evento / Culto</option>
                        </select>
                    </div>

                    <!-- Selección de Célula -->
                    <div id="div-reporte-celula">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 block">Seleccionar Célula</label>
                        <select class="w-full rounded-xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-3.5 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 dark:focus:border-cyan-500 transition-all shadow-sm mb-3.5" id="select-reporte-celula">
                            <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Seleccione una célula...</option>
                            @php $celulas = \App\Models\Celula::orderBy('nombre')->get(); @endphp
                            @foreach($celulas as $cel)
                                <option value="{{ $cel->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">{{ $cel->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="generarReporteAsistencia('celula')" class="btn-bento-asistencia w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer">
                            <i class="fas fa-table-cells text-base"></i>
                            <span>Matriz Mensual de Célula</span>
                        </button>
                    </div>

                    <!-- Selección de Evento -->
                    <div id="div-reporte-evento" style="display: none;">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 block">Seleccionar Evento / Culto</label>
                        <select class="w-full rounded-xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-3.5 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 dark:focus:border-cyan-500 transition-all shadow-sm mb-3.5" id="select-reporte-evento">
                            <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Seleccione un evento...</option>
                            @php $eventosRep = \App\Models\Evento::orderBy('fecha_inicio', 'asc')->get(); @endphp
                            @foreach($eventosRep as $evRep)
                                <option value="{{ $evRep->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">{{ \Carbon\Carbon::parse($evRep->fecha_inicio)->translatedFormat('d M Y') }} - {{ $evRep->titulo }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="generarReporteAsistencia('evento')" class="btn-bento-asistencia w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer">
                            <i class="fas fa-list-check text-base"></i>
                            <span>Lista de Asistencia al Evento</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             5. APORTES POR FAMILIA (ROSA / ROJO)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rose-500/10 dark:bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/20 transition-all duration-500"></div>

            <div>
                <!-- Header Card -->
                <div class="flex items-center gap-4 mb-5">
                    <div class="report-icon-box icon-box-familia group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-people-roof"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Aportes por Familia</h5>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">
                            <i class="fas fa-heart text-xs"></i> Compromiso Integral
                        </span>
                    </div>
                </div>
                <!-- Descripción -->
                <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-6">
                    Consolidado de ingresos y aportaciones agrupados por núcleo familiar para medir y agradecer el compromiso económico integral con la visión de la iglesia.
                </p>
            </div>

            <!-- Acción -->
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <a href="{{ route('reportes.ingresos_familia') }}" target="_blank" class="btn-bento-familia w-full py-3.5 px-5 rounded-xl font-bold text-xs flex items-center justify-center gap-2.5 cursor-pointer no-underline">
                    <i class="fas fa-file-invoice-dollar text-base"></i>
                    <span>Reporte de Aportes Familiares</span>
                </a>
            </div>
        </div>

        <!-- ==========================================
             6. REPORTE DE VOTACIONES Y ELECCIONES (VINO / ORO)
        ========================================== -->
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm hover:shadow-xl flex flex-col justify-between relative overflow-hidden group md:col-span-2 lg:col-span-3">
            <!-- Glow de fondo -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-red-900/10 dark:bg-red-950/5 rounded-full blur-3xl group-hover:bg-red-900/20 transition-all duration-500"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center my-auto">
                <!-- Columna Izquierda: Información y Descripción -->
                <div>
                    <!-- Header Card -->
                    <div class="flex items-center gap-4 mb-5">
                        <div class="report-icon-box icon-box-votaciones group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-check-to-slot"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Votaciones y Elecciones</h5>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/20">
                                <i class="fas fa-box-archive text-xs"></i> Escrutinio y Directivas
                            </span>
                        </div>
                    </div>
                    <!-- Descripción -->
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-0">
                        Genera las actas oficiales de escrutinio electoral, visualiza y descarga el padrón de participantes que ejercieron su voto, y obtén el reporte definitivo de la conformación de la organización con la junta directiva electa.
                    </p>
                </div>

                <!-- Columna Derecha: Selección y Botones de Acción -->
                <div class="space-y-4 bg-slate-50/60 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-inner">
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 block">Seleccionar Proceso Electoral</label>
                        <select class="w-full rounded-xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-3.5 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-700 dark:focus:border-red-700 transition-all shadow-sm" id="select-reporte-eleccion">
                            <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Seleccione una elección...</option>
                            @foreach($elecciones as $elec)
                                <option value="{{ $elec->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">
                                    {{ $elec->organizacion->nombre }} - {{ $elec->titulo }} ({{ ucfirst($elec->estado) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2">
                        <button type="button" onclick="generarReporteVotacion('escrutinio')" class="btn-bento-votaciones py-3 px-3 rounded-xl font-bold text-[10px] flex items-center justify-center gap-1.5 cursor-pointer text-center">
                            <i class="fas fa-file-contract text-sm"></i>
                            <span>Acta Escrutinio</span>
                        </button>
                        <button type="button" onclick="generarReporteVotacion('participantes')" class="btn-bento-votaciones py-3 px-3 rounded-xl font-bold text-[10px] flex items-center justify-center gap-1.5 cursor-pointer text-center">
                            <i class="fas fa-users text-sm"></i>
                            <span>¿Quiénes Votaron?</span>
                        </button>
                        <button type="button" onclick="generarReporteVotacion('conformacion')" class="btn-bento-votaciones py-3 px-3 rounded-xl font-bold text-[10px] flex items-center justify-center gap-1.5 cursor-pointer text-center">
                            <i class="fas fa-sitemap text-sm"></i>
                            <span>Conformación Org.</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ==========================================
         MODAL: PARÁMETROS DEL REPORTE DE TESORERÍA
    ========================================== -->
    <div x-cloak x-show="showTesoreriaModal">
        <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop con desenfoque -->
            <div @click="showTesoreriaModal = false" 
                 x-show="showTesoreriaModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 backdrop-blur-none"
                 x-transition:enter-end="opacity-100 backdrop-blur-md"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 backdrop-blur-md"
                 x-transition:leave-end="opacity-0 backdrop-blur-none"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-md"></div>
            
            <!-- Contenedor de Posicionamiento -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10">
                <!-- Panel Modal -->
                <div x-show="showTesoreriaModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     @click.outside="showTesoreriaModal = false"
                     @keydown.escape.window="showTesoreriaModal = false"
                     class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
                    
                    <!-- Header Modal -->
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                        <div class="flex items-center gap-4">
                            <div class="stat-icon-box bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 shadow-sm flex-shrink-0">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight" id="modal-title">Parámetros del Reporte</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Configuración de filtros para el corte de caja</p>
                            </div>
                        </div>
                        <button @click="showTesoreriaModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer border-0 bg-transparent">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('reportes.tesoreria.pdf') }}" method="GET" target="_blank" @submit="setTimeout(() => showTesoreriaModal = false, 500)" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Desde -->
                            <div class="flex flex-col gap-2">
                                <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-emerald-500"></i> Fecha Inicial (Desde)
                                </label>
                                <input type="date" name="fecha_inicio" value="{{ now()->startOfMonth()->format('Y-m-d') }}" required
                                       class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm">
                            </div>
                            <!-- Hasta -->
                            <div class="flex flex-col gap-2">
                                <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                    <i class="fas fa-calendar-check text-emerald-500"></i> Fecha Final (Hasta)
                                </label>
                                <input type="date" name="fecha_fin" value="{{ now()->format('Y-m-d') }}" required
                                       class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm">
                            </div>
                        </div>

                        <!-- Caja Específica -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-boxes-stacked text-emerald-500"></i> Filtrar por Caja (Opcional)
                            </label>
                            <select name="account_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm">
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Todas las Cajas (Consolidado)</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">{{ $acc->name }} (Balance Actual: Q{{ number_format($acc->initial_balance + ($acc->total_balance ?? 0), 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Movimiento -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-filter text-emerald-500"></i> Tipo de Movimiento
                            </label>
                            <select name="type" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm">
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Todos (Ingresos y Gastos)</option>
                                <option value="income" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Solo Ingresos (+)</option>
                                <option value="expense" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Solo Gastos (-)</option>
                            </select>
                        </div>

                        <!-- Footer Modal -->
                        <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80 mt-2">
                            <button type="button" @click="showTesoreriaModal = false" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all cursor-pointer">Cancelar</button>
                            <button type="submit" class="btn-bento-finanzas w-full sm:w-auto px-8 py-3.5 rounded-2xl font-bold text-sm transition-all border-0 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-print text-lg"></i> Generar PDF Oficial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL: PARÁMETROS DEL REPORTE DE INVENTARIO
    ========================================== -->
    <div x-cloak x-show="showInventarioModal">
        <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-inventario-title" role="dialog" aria-modal="true">
            <!-- Backdrop con desenfoque -->
            <div @click="showInventarioModal = false" 
                 x-show="showInventarioModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 backdrop-blur-none"
                 x-transition:enter-end="opacity-100 backdrop-blur-md"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 backdrop-blur-md"
                 x-transition:leave-end="opacity-0 backdrop-blur-none"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-md"></div>
            
            <!-- Contenedor de Posicionamiento -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10">
                <!-- Panel Modal -->
                <div x-show="showInventarioModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     @click.outside="showInventarioModal = false"
                     @keydown.escape.window="showInventarioModal = false"
                     class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
                    
                    <!-- Header Modal -->
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                        <div class="flex items-center gap-4">
                            <div class="stat-icon-box bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-100 dark:border-violet-500/20 shadow-sm flex-shrink-0">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight" id="modal-inventario-title">Filtros de Inventario</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Configuración de filtros para el reporte</p>
                            </div>
                        </div>
                        <button @click="showInventarioModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer border-0 bg-transparent">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('reportes.inventario') }}" method="GET" target="_blank" @submit="setTimeout(() => showInventarioModal = false, 500)" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                        
                        <!-- Estado Específico -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-star-half-stroke text-violet-500"></i> Filtrar por Estado
                            </label>
                            <select name="estado" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:focus:border-violet-500 transition-all shadow-sm">
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Todos los Estados</option>
                                <option value="Nuevo" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Nuevo</option>
                                <option value="Bueno" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Bueno</option>
                                <option value="Regular" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Regular</option>
                                <option value="Malo" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Malo</option>
                            </select>
                        </div>

                        <!-- Ubicación Específica -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-location-dot text-violet-500"></i> Filtrar por Ubicación
                            </label>
                            <select name="ubicacion" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:focus:border-violet-500 transition-all shadow-sm">
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Todas las Ubicaciones</option>
                                @foreach($ubicacionesInventario as $ubicacion)
                                    <option value="{{ $ubicacion }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">{{ $ubicacion }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Responsable Específico -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-user-tie text-violet-500"></i> Filtrar por Responsable
                            </label>
                            <select name="responsable_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:focus:border-violet-500 transition-all shadow-sm">
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Todos los Responsables</option>
                                @foreach($responsablesInventario as $responsable)
                                    <option value="{{ $responsable->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">{{ $responsable->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Footer Modal -->
                        <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80 mt-2">
                            <button type="button" @click="showInventarioModal = false" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all cursor-pointer">Cancelar</button>
                            <button type="submit" class="btn-bento-inventario w-full sm:w-auto px-8 py-3.5 rounded-2xl font-bold text-sm transition-all border-0 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-print text-lg"></i> Generar PDF Oficial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL: PARÁMETROS DEL REPORTE DE MEMBRESÍA
    ========================================== -->
    <div x-cloak x-show="showMembresiaModal">
        <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-membresia-title" role="dialog" aria-modal="true">
            <div @click="showMembresiaModal = false" 
                 x-show="showMembresiaModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 backdrop-blur-none"
                 x-transition:enter-end="opacity-100 backdrop-blur-md"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 backdrop-blur-md"
                 x-transition:leave-end="opacity-0 backdrop-blur-none"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-md"></div>
            
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10">
                <div x-show="showMembresiaModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     @click.outside="showMembresiaModal = false"
                     @keydown.escape.window="showMembresiaModal = false"
                     class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
                    
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                        <div class="flex items-center gap-4">
                            <div class="stat-icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 shadow-sm flex-shrink-0">
                                <i class="fas fa-users-viewfinder"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight" id="modal-membresia-title">Filtros de Membresía</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Configuración del censo congregacional</p>
                            </div>
                        </div>
                        <button @click="showMembresiaModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer border-0 bg-transparent">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('reportes.membresia') }}" method="GET" target="_blank" @submit="setTimeout(() => showMembresiaModal = false, 500)" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                        
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-list text-blue-500"></i> Tipo de Censo
                            </label>
                            <select name="tipo" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500 transition-all shadow-sm">
                                <option value="general" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Censo General (Todos los miembros)</option>
                                <option value="bautizados" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Solo Miembros Bautizados</option>
                                <option value="no_bautizados" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Miembros No Bautizados</option>
                            </select>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80 mt-2">
                            <button type="button" @click="showMembresiaModal = false" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all cursor-pointer">Cancelar</button>
                            <button type="submit" class="btn-bento-membresia w-full sm:w-auto px-8 py-3.5 rounded-2xl font-bold text-sm transition-all border-0 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-print text-lg"></i> Generar PDF Oficial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL: PARÁMETROS DEL REPORTE DE ORGANIZACIONES
    ========================================== -->
    <div x-cloak x-show="showOrganizacionesModal">
        <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-organizaciones-title" role="dialog" aria-modal="true">
            <div @click="showOrganizacionesModal = false" 
                 x-show="showOrganizacionesModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 backdrop-blur-none"
                 x-transition:enter-end="opacity-100 backdrop-blur-md"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 backdrop-blur-md"
                 x-transition:leave-end="opacity-0 backdrop-blur-none"
                 class="fixed inset-0 bg-slate-950/40 backdrop-blur-md"></div>
            
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10">
                <div x-show="showOrganizacionesModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                     @click.outside="showOrganizacionesModal = false"
                     @keydown.escape.window="showOrganizacionesModal = false"
                     class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
                    
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                        <div class="flex items-center gap-4">
                            <div class="stat-icon-box bg-pink-50 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400 border border-pink-100 dark:border-pink-500/20 shadow-sm flex-shrink-0">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight" id="modal-organizaciones-title">Reporte de Organizaciones</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Estructura y directivas actuales</p>
                            </div>
                        </div>
                        <button @click="showOrganizacionesModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer border-0 bg-transparent">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('reportes.organizaciones') }}" method="GET" target="_blank" @submit="setTimeout(() => showOrganizacionesModal = false, 500)" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                        
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-layer-group text-pink-500"></i> Agrupación del Reporte
                            </label>
                            <select name="modo_reporte" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 dark:focus:border-pink-500 transition-all shadow-sm">
                                <option value="organizacion" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Agrupar por Organización (Estructura Interna)</option>
                                <option value="puesto" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Agrupar por Puesto/Cargo (Ej. Todos los Presidentes)</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-filter text-pink-500"></i> Filtrar por Organización (Opcional)
                            </label>
                            <select name="organizacion_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 dark:focus:border-pink-500 transition-all shadow-sm">
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">Todas las Organizaciones</option>
                                @foreach($organizacionesReporte as $org)
                                    <option value="{{ $org->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-800">{{ $org->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80 mt-2">
                            <button type="button" @click="showOrganizacionesModal = false" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all cursor-pointer">Cancelar</button>
                            <button type="submit" class="btn-bento-organizaciones w-full sm:w-auto px-8 py-3.5 rounded-2xl font-bold text-sm transition-all border-0 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-print text-lg"></i> Generar PDF Oficial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAsistenciaReportType() {
        const val = document.getElementById('select-tipo-asistencia-reporte').value;
        if (val === 'celula') {
            document.getElementById('div-reporte-celula').style.display = 'block';
            document.getElementById('div-reporte-evento').style.display = 'none';
        } else {
            document.getElementById('div-reporte-evento').style.display = 'block';
            document.getElementById('div-reporte-celula').style.display = 'none';
        }
    }

    function generarReporteAsistencia(tipo) {
        if (tipo === 'celula') {
            const celulaId = document.getElementById('select-reporte-celula').value;
            if (!celulaId) { alert('Por favor selecciona una célula'); return; }
            const url = "{{ route('reportes.asistencia_celula', ':id') }}".replace(':id', celulaId);
            window.open(url, '_blank');
        } else {
            const eventoId = document.getElementById('select-reporte-evento').value;
            if (!eventoId) { alert('Por favor selecciona un evento'); return; }
            const url = "{{ route('reportes.asistencia_evento', ':id') }}".replace(':id', eventoId);
            window.open(url, '_blank');
        }
    }

    function generarReporteVotacion(tipo) {
        const eleccionId = document.getElementById('select-reporte-eleccion').value;
        if (!eleccionId) { alert('Por favor selecciona un proceso electoral'); return; }
        
        let url = '';
        if (tipo === 'escrutinio') {
            url = "{{ route('reportes.votaciones.escrutinio', ':id') }}".replace(':id', eleccionId);
        } else if (tipo === 'participantes') {
            url = "{{ route('reportes.votaciones.participantes', ':id') }}".replace(':id', eleccionId);
        } else if (tipo === 'conformacion') {
            url = "{{ route('reportes.votaciones.conformacion', ':id') }}".replace(':id', eleccionId);
        }
        window.open(url, '_blank');
    }
</script>
@endpush
