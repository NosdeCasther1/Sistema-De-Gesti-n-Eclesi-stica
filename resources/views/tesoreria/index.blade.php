@extends('layouts.app')

@section('title', 'Tesorería - AD Rey de Reyes')

@section('header_title', 'Tesorería y Finanzas')
@section('header_subtitle', 'Control de ingresos y egresos ministeriales por fondos y cajas')
@section('header_icon')
<i class="fas fa-wallet fs-5"></i>
@endsection

@push('styles')
<style>
    .main-content > main {
        padding-top: calc(60px + 0.75rem) !important;
    }
    @media (max-width: 991.98px) {
        .main-content > main {
            padding-top: calc(56px + 0.75rem) !important;
        }
    }
</style>
@endpush

@section('content')
<div x-data="{ 
    showIncomeModal: false, 
    showExpenseModal: false, 
    showTransferModal: false,
    activeTab: '{{ $activeTab ?? 'all' }}',
    accountNames: {
        'all': 'Todas las cajas',
        @foreach($accounts as $acc)
        '{{ $acc->id }}': '{{ $acc->name }}',
        @endforeach
    },
    isFetching: false,
    switchTab(tab, url) {
        if (this.activeTab === tab) return;
        this.activeTab = tab;
        this.isFetching = true;
        const searchInput = document.getElementById('searchTabInput');
        if (searchInput) searchInput.value = tab;
        window.history.pushState(null, '', url);
        
        fetch(url)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const elIngresos = document.getElementById('total-ingresos-val');
                const elGastos = document.getElementById('total-gastos-val');
                const elBalance = document.getElementById('balance-val');
                const elTable = document.getElementById('table-results');
                
                if (elIngresos && doc.getElementById('total-ingresos-val')) elIngresos.innerHTML = doc.getElementById('total-ingresos-val').innerHTML;
                if (elGastos && doc.getElementById('total-gastos-val')) elGastos.innerHTML = doc.getElementById('total-gastos-val').innerHTML;
                if (elBalance && doc.getElementById('balance-val')) elBalance.innerHTML = doc.getElementById('balance-val').innerHTML;
                if (elTable && doc.getElementById('table-results')) elTable.innerHTML = doc.getElementById('table-results').innerHTML;
                
                this.isFetching = false;
            })
            .catch(() => { window.location.href = url; });
    }
}" class="w-full transition-colors duration-300 antialiased font-sans">
    <div class="max-w-[1600px] mx-auto space-y-6">
        
        {{-- CAPA 1: HEADER & ACCIONES PRINCIPALES --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                    <i class="fa-solid fa-vault text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Panel de Control Financiero</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Gestión contable inmutable por fondos ministeriales.</p>
                </div>
            </div>
            
            {{-- BOTONES DE ACCIÓN (Movidos a la derecha para no romper jerarquía de lectura) --}}
            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <button @click="showIncomeModal = true" class="flex-1 md:flex-none py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-sm text-xs flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-circle-plus mr-2"></i> Nuevo Ingreso
                </button>
                <button @click="showExpenseModal = true" class="flex-1 md:flex-none py-2.5 px-4 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-sm text-xs flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-circle-minus mr-2"></i> Registrar Gasto
                </button>
                <button @click="showTransferModal = true" class="w-full md:w-auto py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-xs flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-right-left mr-2"></i> Transferir Fondos
                </button>
            </div>
        </div>

        {{-- CAPA 2: CONTEXTO (FILTROS DE CAJAS) --}}
        <div class="flex overflow-x-auto custom-scrollbar gap-2 pb-2">
            <a href="{{ route('tesoreria.index', ['tab' => 'all', 'search' => request('search')]) }}" 
               @click.prevent="switchTab('all', $el.href)"
               :class="activeTab == 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition-colors'"
               class="whitespace-nowrap py-2 px-5 font-bold rounded-xl text-xs flex items-center no-underline">
                <i class="fa-solid fa-layer-group mr-2"></i> Todas las cajas
            </a>
            @foreach($accounts as $account)
            <a href="{{ route('tesoreria.index', ['tab' => $account->id, 'search' => request('search')]) }}" 
               @click.prevent="switchTab('{{ $account->id }}', $el.href)"
               :class="activeTab == '{{ $account->id }}' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition-colors'"
               class="whitespace-nowrap py-2 px-5 font-bold rounded-xl text-xs flex items-center transition-colors no-underline">
                <i class="fa-solid fa-box-archive mr-2 text-slate-400"></i> {{ $account->name }}
            </a>
            @endforeach
        </div>

        {{-- CAPA 3: RESUMEN FINANCIERO (KPIs) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Ingresos --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Ingresos</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </div>
                <div>
                    <h2 id="total-ingresos-val" class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">Q{{ number_format($totalIngresos, 2) }}</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 font-medium"><i class="fa-solid fa-lock mr-1"></i> Registros inmutables auditados</p>
                </div>
            </div>
            {{-- Egresos --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Gastos</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                </div>
                <div>
                    <h2 id="total-gastos-val" class="text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight">Q{{ number_format($totalGastos, 2) }}</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 font-medium"><i class="fa-solid fa-lock mr-1"></i> Partidas de egreso autorizadas</p>
                </div>
            </div>
            {{-- Balance --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Balance Consolidado</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>
                <div>
                    <h2 id="balance-val" class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">Q{{ number_format($balanceGeneral, 2) }}</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 font-medium"><i class="fa-solid fa-coins mr-1"></i> Disponibilidad financiera total</p>
                </div>
            </div>
        </div>

        {{-- CAPA 4: DETALLE (TABLA DE MOVIMIENTOS) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            {{-- Barra de herramientas de tabla --}}
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-slate-950/50">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center">
                        <i class="fa-solid fa-book mr-2 text-indigo-500"></i> Libro Diario de Movimientos
                    </h3>
                    <span class="hidden sm:inline-flex px-2 py-0.5 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[9px] font-bold rounded uppercase tracking-widest"><i class="fa-solid fa-shield-halved mr-1"></i> Auditoría Estricta</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 text-[9px] font-bold rounded uppercase tracking-widest">
                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                        <span x-text="accountNames[activeTab] || 'Todas las cajas'"></span>
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3 items-center">
                    <div class="relative w-full md:w-64">
                        <form action="{{ route('tesoreria.index') }}" method="GET" id="searchForm" @submit.prevent class="m-0 w-full relative flex items-center">
                            <input type="hidden" name="tab" id="searchTabInput" value="{{ $activeTab ?? 'all' }}">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" name="search" id="searchInput" 
                                   class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none text-slate-700 dark:text-slate-200"
                                   placeholder="Buscar por descripción..." value="{{ request('search') }}" autocomplete="off">
                            <button type="button" id="clearSearchBtn" class="absolute right-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all flex items-center justify-center p-1 border-0 bg-transparent cursor-pointer" style="display: {{ request('search') ? 'flex' : 'none' }};">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('reportes.tesoreria') }}" target="_blank" class="w-full sm:w-auto shrink-0 py-2 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold rounded-xl text-xs shadow-sm transition-colors flex items-center justify-center no-underline">
                        <i class="fa-solid fa-file-pdf mr-2 text-rose-500"></i> Exportar PDF
                    </a>
                </div>
            </div>

            {{-- Tabla Responsiva --}}
            <div id="table-results" class="overflow-x-auto custom-scrollbar">
                @include('tesoreria._table')
            </div>
        </div>

        {{-- Bitácora de Ajustes a Cajas y Fondos (Auditoría) --}}
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
            <!-- Glow de fondo -->
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

            <div>
                {{-- Header Bento --}}
                <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Bitácora de Ajustes (Auditoría)</h5>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Registro y reporte de modificaciones críticas en Cajas/Fondos.</p>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm mb-4">
                    <div class="overflow-x-auto max-h-[350px] overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800/80 sticky top-0 z-10">
                                <tr>
                                    <th class="pl-6 pr-4 py-4">Fecha y Cuenta</th>
                                    <th class="py-4">Campo</th>
                                    <th class="py-4">Ajuste Realizado</th>
                                    <th class="py-4">Justificación / Responsable</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50 text-xs">
                                @forelse($adjustments as $adj)
                                    <tr class="transition-all hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                                        <td class="pl-6 pr-4 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $adj->account->name ?? 'Cuenta Eliminada' }}</div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">{{ $adj->created_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="py-4 font-semibold">
                                            @if($adj->field_changed === 'name')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 uppercase tracking-wider">Nombre</span>
                                            @elseif($adj->field_changed === 'initial_balance')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 uppercase tracking-wider">Saldo Inicial</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20 uppercase tracking-wider">{{ $adj->field_changed }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 pr-4">
                                            @if($adj->field_changed === 'initial_balance')
                                                <div class="flex items-center gap-1.5 font-mono">
                                                    <span class="text-rose-500 dark:text-rose-400 line-through">Q{{ number_format((float)$adj->old_value, 2) }}</span>
                                                    <i class="fas fa-arrow-right text-[10px] text-slate-400"></i>
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Q{{ number_format((float)$adj->new_value, 2) }}</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-slate-500 dark:text-slate-400 line-through text-[11px] truncate max-w-[120px]" title="{{ $adj->old_value }}">{{ $adj->old_value }}</span>
                                                    <i class="fas fa-arrow-right text-[10px] text-slate-400"></i>
                                                    <span class="text-slate-900 dark:text-white font-bold text-[11px] truncate max-w-[120px]" title="{{ $adj->new_value }}">{{ $adj->new_value }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 pr-6">
                                            <div class="text-slate-700 dark:text-slate-300 font-medium italic mb-0.5 max-w-[220px] break-words">« {{ $adj->justification }} »</div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold flex items-center gap-1">
                                                <i class="fas fa-user-shield text-[9px] text-blue-500"></i>
                                                <span>{{ $adj->user->nombre ?? ($adj->user->name ?? 'Sistema/Admin') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 dark:text-slate-600 italic">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i class="fas fa-history text-2xl"></i>
                                                <span>No se han registrado ajustes o auditorías de saldo todavía.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-auto">
                <i class="fas fa-info-circle text-blue-500 text-base"></i>
                <span>Registro de auditoría inmutable obligatorio para transparencia contable.</span>
            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL: NUEVO INGRESO (VERDE)
    ========================================== -->
    <div x-cloak 
         x-show="showIncomeModal" 
         class="fixed inset-0 z-[9999] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showIncomeModal"
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
            <div x-show="showIncomeModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 @click.outside="showIncomeModal = false"
                 @keydown.escape.window="showIncomeModal = false"
                 class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
            
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                            <i class="fas fa-coins text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Registrar Nuevo Ingreso</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Entrada de fondos ministeriales con auditoría inmutable</p>
                        </div>
                    </div>
                    <button @click="showIncomeModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('tesoreria.store') }}" method="POST" enctype="multipart/form-data" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                    @csrf
                    <input type="hidden" name="type" value="income">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Caja de Destino -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-box-archive text-emerald-500"></i> Caja de Destino
                            </label>
                            <select name="account_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm" required>
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">Selecciona una caja ministerial...</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">{{ $acc->name }} (Saldo: Q{{ number_format($acc->initial_balance, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Categoría -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-tags text-emerald-500"></i> Categoría / Partida
                            </label>
                            <select name="categoria_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm" required>
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">Selecciona una partida...</option>
                                @foreach($incomeCategories as $cat)
                                    <option value="{{ $cat->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Monto -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-circle-dollar-to-slot text-emerald-500"></i> Monto del Ingreso
                            </label>
                            <div class="relative flex items-center rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500/20 focus-within:border-emerald-500 dark:focus-within:border-emerald-500 transition-all shadow-sm">
                                <span class="absolute left-5 text-slate-900 dark:text-slate-400 font-black text-xl">Q</span>
                                <input type="number" name="monto" step="0.01" min="0.01" onwheel="return false;" onkeydown="if(event.key === 'ArrowUp' || event.key === 'ArrowDown') event.preventDefault();" class="w-full bg-transparent py-4 pl-14 pr-6 text-right text-3xl font-bold text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-500 focus:outline-none tracking-tight appearance-none" placeholder="0.00" required>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-calendar-day text-emerald-500"></i> Fecha de Transacción
                            </label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-4 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm" required>
                        </div>

                        <!-- Número de Referencia -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2 justify-between">
                                <span class="flex items-center gap-2"><i class="fas fa-hashtag text-emerald-500"></i> Número de Referencia</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-500 lowercase">(Opcional)</span>
                            </label>
                            <input type="text" name="reference_number" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm placeholder-slate-500 dark:placeholder-slate-500" placeholder="Ej. DEP-123456, CHQ-987..." autocomplete="off">
                        </div>

                        <!-- Descripción -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-pen-to-square text-emerald-500"></i> Descripción del Ingreso
                            </label>
                            <input type="text" name="descripcion" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all shadow-sm placeholder-slate-500 dark:placeholder-slate-500" placeholder="Ej. Diezmos de la congregación, ofrenda especial..." required autocomplete="off">
                        </div>

                        <!-- Comprobante -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2 justify-between">
                                <span class="flex items-center gap-2"><i class="fas fa-cloud-arrow-up text-emerald-500"></i> Comprobante / Recibo Físico</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-400 lowercase">(Opcional)</span>
                            </label>
                            <div class="rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 p-2 transition-all shadow-sm flex items-center">
                                <input type="file" name="proof_path" accept="image/*,application/pdf" class="w-full text-sm text-slate-500 dark:text-slate-400 file:bg-slate-100 dark:file:bg-slate-700 file:text-slate-700 dark:file:text-slate-200 file:border-0 file:rounded-lg file:px-4 file:py-2 file:mr-4 file:font-semibold hover:file:bg-slate-200 dark:hover:file:bg-slate-600 transition-all cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80">
                        <button type="button" @click="showIncomeModal = false" class="w-full sm:w-auto btn px-5 py-2.5 rounded-xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all">Cancelar</button>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all border-0 flex items-center justify-center gap-2">
                            <i class="fas fa-circle-check text-lg"></i> Confirmar Ingreso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL: REGISTRAR GASTO (ROJO)
    ========================================== -->
    <div x-cloak 
         x-show="showExpenseModal" 
         class="fixed inset-0 z-[9999] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showExpenseModal"
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
            <div x-show="showExpenseModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 @click.outside="showExpenseModal = false"
                 @keydown.escape.window="showExpenseModal = false"
                 class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
            
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20 shadow-sm">
                            <i class="fas fa-file-invoice-dollar text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Registrar Salida / Gasto</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Egreso de fondos ministeriales con rastro de auditoría</p>
                        </div>
                    </div>
                    <button @click="showExpenseModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Form -->
                <form action="{{ route('tesoreria.store') }}" method="POST" enctype="multipart/form-data" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                    @csrf
                    <input type="hidden" name="type" value="expense">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Caja de Origen -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-box-open text-rose-500"></i> Caja de Origen
                            </label>
                            <select name="account_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:focus:border-rose-500 transition-all shadow-sm" required>
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">Selecciona una caja ministerial...</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">{{ $acc->name }} (Saldo: Q{{ number_format($acc->initial_balance, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Categoría -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-tags text-rose-500"></i> Categoría / Partida
                            </label>
                            <select name="categoria_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:focus:border-rose-500 transition-all shadow-sm" required>
                                <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">Selecciona una partida...</option>
                                @foreach($expenseCategories as $cat)
                                    <option value="{{ $cat->id }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Monto -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-circle-dollar-to-slot text-rose-500"></i> Monto del Gasto
                            </label>
                            <div class="relative flex items-center rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 overflow-hidden focus-within:ring-2 focus-within:ring-rose-500/20 focus-within:border-rose-500 dark:focus-within:border-rose-500 transition-all shadow-sm">
                                <span class="absolute left-5 text-slate-900 dark:text-slate-400 font-black text-xl">Q</span>
                                <input type="number" name="monto" step="0.01" min="0.01" onwheel="return false;" onkeydown="if(event.key === 'ArrowUp' || event.key === 'ArrowDown') event.preventDefault();" class="w-full bg-transparent py-4 pl-14 pr-6 text-right text-3xl font-bold text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-500 focus:outline-none tracking-tight appearance-none" placeholder="0.00" required>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="flex flex-col gap-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-calendar-day text-rose-500"></i> Fecha de Transacción
                            </label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-4 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:focus:border-rose-500 transition-all shadow-sm" required>
                        </div>

                        <!-- Número de Referencia -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2 justify-between">
                                <span class="flex items-center gap-2"><i class="fas fa-hashtag text-rose-500"></i> Número de Referencia</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-500 lowercase">(Opcional)</span>
                            </label>
                            <input type="text" name="reference_number" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:focus:border-rose-500 transition-all shadow-sm placeholder-slate-500 dark:placeholder-slate-500" placeholder="Ej. DEP-123456, CHQ-987..." autocomplete="off">
                        </div>

                        <!-- Descripción -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-pen-to-square text-rose-500"></i> Descripción del Gasto
                            </label>
                            <input type="text" name="descripcion" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:focus:border-rose-500 transition-all shadow-sm placeholder-slate-500 dark:placeholder-slate-500" placeholder="Ej. Pago de factura eléctrica del mes..." required autocomplete="off">
                        </div>

                        <!-- Comprobante -->
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2 justify-between">
                                <span class="flex items-center gap-2"><i class="fas fa-cloud-arrow-up text-rose-500"></i> Comprobante / Factura Físico</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-400 lowercase">(Opcional)</span>
                            </label>
                            <div class="rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 p-2 transition-all shadow-sm flex items-center">
                                <input type="file" name="proof_path" accept="image/*,application/pdf" class="w-full text-sm text-slate-500 dark:text-slate-400 file:bg-slate-100 dark:file:bg-slate-700 file:text-slate-700 dark:file:text-slate-200 file:border-0 file:rounded-lg file:px-4 file:py-2 file:mr-4 file:font-semibold hover:file:bg-slate-200 dark:hover:file:bg-slate-600 transition-all cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80">
                        <button type="button" @click="showExpenseModal = false" class="w-full sm:w-auto btn px-5 py-2.5 rounded-xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all">Cancelar</button>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-rose-600 text-white font-bold text-sm hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/20 shadow-lg shadow-rose-500/20 hover:shadow-rose-500/30 transition-all border-0 flex items-center justify-center gap-2">
                            <i class="fas fa-circle-check text-lg"></i> Confirmar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==========================================
         MODAL: TRANSFERENCIA ENTRE CAJAS (VIOLETA/ÍNDIGO)
    ========================================== -->
    @include('tesoreria.partials.modal-transferencia')
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; }
</style>
@endsection

@push('styles')
<style>
    /* Ocultar botones de incremento/decremento */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type="number"] { -moz-appearance: textfield; }

    /* Evitar flash de modales Alpine.js al cargar la página */
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const resultsContainer = document.getElementById('table-results');
        const clearBtn = document.getElementById('clearSearchBtn');
        let debounceTimer;

        const toggleClearBtn = () => {
            if (clearBtn) {
                if (searchInput && searchInput.value.trim().length > 0) {
                    clearBtn.style.setProperty('display', 'inline-flex', 'important');
                } else {
                    clearBtn.style.setProperty('display', 'none', 'important');
                }
            }
        };

        const performSearch = () => {
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData).toString();
            const url = `${searchForm.action}?${params}`;

            resultsContainer.style.opacity = '0.5';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                resultsContainer.innerHTML = html;
                resultsContainer.style.opacity = '1';
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                resultsContainer.style.opacity = '1';
            });
        };

        if(searchInput && searchForm) {
            searchInput.addEventListener('input', function() {
                toggleClearBtn();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSearch, 300);
            });

            // Prevent manual form submission (reloads)
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });
        }

        if(clearBtn && searchInput) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearBtn();
                performSearch();
                searchInput.focus();
            });
        }

        // Initial toggle on page load if search already populated
        toggleClearBtn();
    });
</script>
@endpush
