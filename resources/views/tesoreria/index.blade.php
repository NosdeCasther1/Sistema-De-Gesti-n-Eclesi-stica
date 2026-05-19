@extends('layouts.app')

@section('title', 'Tesorería - AD Rey de Reyes')

@section('header_title', 'Tesorería y Finanzas')
@section('header_subtitle', 'Control de ingresos y egresos ministeriales por fondos y cajas')
@section('header_icon')
<i class="fas fa-wallet fs-5"></i>
@endsection

@section('content')
<!-- Contenedor Principal Alpine.js -->
<div x-data="{ 
    showIncomeModal: false, 
    showExpenseModal: false, 
    showTransferModal: false,
    activeTab: '{{ $activeTab ?? 'all' }}',
    isFetching: false,
    switchTab(tab, url) {
        if (this.activeTab === tab) return;
        this.activeTab = tab;
        this.isFetching = true;
        const searchInput = document.getElementById('searchTabInput');
        if (searchInput) searchInput.value = tab;
        window.history.pushState(null, '', url);
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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
}" class="flex flex-col h-[calc(100vh-180px)] min-h-0 overflow-hidden">

    <!-- Panel Superior Estático (SaaS Bento Compacto) -->
    <div class="shrink-0 flex-none pb-3">
        <!-- Botones de Acción Superior -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-slate-800 dark:text-white d-flex align-items-center gap-2" style="font-size: 1.15rem;">
                    <i class="fas fa-chart-line" style="background: linear-gradient(135deg, #3b82f6, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i> Panel de Control Financiero
                </h5>
                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Gestión contable inmutable por fondos ministeriales</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button @click="showIncomeModal = true" class="btn-action-treasury btn-income">
                    <i class="fas fa-plus-circle"></i> <span>Nuevo Ingreso</span>
                </button>
                <button @click="showExpenseModal = true" class="btn-action-treasury btn-expense">
                    <i class="fas fa-minus-circle"></i> <span>Registrar Gasto</span>
                </button>
                <button @click="showTransferModal = true" class="btn-action-treasury btn-transfer">
                    <i class="fas fa-exchange-alt"></i> <span>Transferir Fondos</span>
                </button>
            </div>
        </div>

        <!-- Selector de Cajas (Premium Glassmorphism Tabs) -->
        <div class="treasury-tabs mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm">
            <div class="d-flex align-items-center gap-1 flex-wrap w-full">
                <a href="{{ route('tesoreria.index', ['tab' => 'all', 'search' => request('search')]) }}" @click.prevent="switchTab('all', $el.href)"
                        :class="activeTab === 'all' ? 'tab-active' : ''"
                        class="tab-pill d-flex align-items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fas fa-layer-group"></i> Todas las cajas
                </a>
                <a href="{{ route('tesoreria.index', ['tab' => 'general', 'search' => request('search')]) }}" @click.prevent="switchTab('general', $el.href)"
                        :class="activeTab === 'general' ? 'tab-active' : ''"
                        class="tab-pill d-flex align-items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fas fa-box-open"></i> Caja General
                </a>
                <a href="{{ route('tesoreria.index', ['tab' => 'jovenes', 'search' => request('search')]) }}" @click.prevent="switchTab('jovenes', $el.href)"
                        :class="activeTab === 'jovenes' ? 'tab-active' : ''"
                        class="tab-pill d-flex align-items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fas fa-users"></i> Caja Jóvenes
                </a>
                <a href="{{ route('tesoreria.index', ['tab' => 'misiones', 'search' => request('search')]) }}" @click.prevent="switchTab('misiones', $el.href)"
                        :class="activeTab === 'misiones' ? 'tab-active' : ''"
                        class="tab-pill d-flex align-items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fas fa-globe-americas"></i> Fondo Misiones
                </a>
            </div>
        </div>

        <!-- Resumen Financiero (Tarjetas Premium con Glassmorphism) -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;" class="mb-3">
            <div>
                <div class="finance-card card-income bg-white dark:bg-slate-900 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-slate-500 dark:text-slate-400 small text-uppercase fw-bold tracking-wider" style="font-size: 0.72rem;">Total Ingresos</div>
                        <div class="card-icon"><i class="fas fa-arrow-trend-up"></i></div>
                    </div>
                    <div id="total-ingresos-val" class="card-amount">Q{{ number_format($totalIngresos, 2) }}</div>
                    <div class="text-slate-400 dark:text-slate-500 d-flex align-items-center gap-1" style="font-size: 0.68rem;">
                        <i class="fas fa-shield-check"></i> <span>Registros inmutables auditados</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="finance-card card-expense bg-white dark:bg-slate-900 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-slate-500 dark:text-slate-400 small text-uppercase fw-bold tracking-wider" style="font-size: 0.72rem;">Total Gastos</div>
                        <div class="card-icon"><i class="fas fa-arrow-trend-down"></i></div>
                    </div>
                    <div id="total-gastos-val" class="card-amount">Q{{ number_format($totalGastos, 2) }}</div>
                    <div class="text-slate-400 dark:text-slate-500 d-flex align-items-center gap-1" style="font-size: 0.68rem;">
                        <i class="fas fa-lock"></i> <span>Partidas de egreso autorizadas</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="finance-card card-balance bg-white dark:bg-slate-900 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="text-slate-500 dark:text-slate-400 small text-uppercase fw-bold tracking-wider" style="font-size: 0.72rem;">Balance Consolidado</div>
                        <div class="card-icon"><i class="fas fa-scale-balanced"></i></div>
                    </div>
                    <div id="balance-val" class="card-amount">Q{{ number_format($balanceGeneral, 2) }}</div>
                    <div class="text-slate-400 dark:text-slate-500 d-flex align-items-center gap-1" style="font-size: 0.68rem;">
                        <i class="fas fa-coins"></i> <span>Disponibilidad financiera total</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Listado de Transacciones (Premium Ledger Container) -->
    <div class="ledger-container bg-white dark:bg-slate-900 shadow-sm flex-1 min-h-0 flex flex-col overflow-hidden relative">
        <div class="ledger-header shrink-0" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 1; min-width: 0; flex-wrap: wrap;">
                <h6 class="fw-bold mb-0 text-slate-800 dark:text-white" style="font-size: 0.95rem; white-space: nowrap; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-book-open" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i> Libro Diario de Movimientos
                </h6>
                <span class="status-indicator bg-slate-100 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400" style="white-space: nowrap;">
                    <i class="fas fa-shield-halved" style="font-size: 0.6rem;"></i> Auditoría Estricta
                </span>
                <span class="status-indicator bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-bold" style="white-space: nowrap;">
                    <i class="fas fa-circle text-xs" style="font-size: 0.45rem; color: #6366f1; animation: pulse 2s infinite;"></i>
                    <span x-text="activeTab === 'all' ? 'Todas las cajas' : (activeTab === 'general' ? 'Caja General' : (activeTab === 'jovenes' ? 'Caja Jóvenes' : 'Fondo Misiones'))"></span>
                </span>
            </div>
            
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap w-full sm:w-auto justify-end">
                <!-- Filtro de Búsqueda Compacto -->
                <div class="search-treasury shadow-sm relative flex-grow sm:flex-grow-0" style="border-radius: 2rem; height: 38px; width: 100%; max-width: 350px; min-width: 240px;">
                    <form action="{{ route('tesoreria.index') }}" method="GET" id="searchForm" @submit.prevent class="m-0 h-full">
                        <input type="hidden" name="tab" id="searchTabInput" value="{{ $activeTab ?? 'all' }}">
                        <div class="flex items-center w-full h-full relative">
                            <span class="absolute left-3 text-slate-400 dark:text-slate-500 pointer-events-none flex items-center justify-center" style="font-size: 0.95rem;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="searchInput" 
                                   class="w-full h-full pl-9 pr-9 py-1 bg-transparent border-0 text-slate-800 dark:text-white font-medium focus:outline-none focus:ring-0 shadow-none text-xs"
                                   style="box-shadow: none !important; background: transparent !important; border: none !important;"
                                   placeholder="Buscar por descripción, miembro, referencia..." value="{{ request('search') }}" autocomplete="off">
                            <button type="button" id="clearSearchBtn" class="absolute right-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-all flex items-center justify-center p-1.5 border-0 bg-transparent cursor-pointer" style="display: {{ request('search') ? 'flex' : 'none' }};">
                                <i class="fas fa-times-circle" style="font-size: 1.1rem;"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <a href="{{ route('reportes.tesoreria') }}" target="_blank" style="flex-shrink: 0; background: linear-gradient(135deg, #64748b, #475569) !important; box-shadow: 0 4px 14px rgba(100,116,139,0.2) !important; font-size: 0.75rem !important; height: 38px; padding: 0 1.25rem !important; white-space: nowrap; border: none; border-radius: 2rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; color: white; text-decoration: none; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-file-pdf"></i> <span>Exportar PDF</span>
                </a>
            </div>
        </div>
        
        <div id="table-results" class="flex-1 min-h-0 overflow-auto custom-scrollbar w-full">
            @include('tesoreria._table')
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
                 @click.away="showIncomeModal = false"
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
                 @click.away="showExpenseModal = false"
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
@endsection

@push('styles')
<style>
    /* Ocultar botones de incremento/decremento */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type="number"] { -moz-appearance: textfield; }

    /* Evitar flash de modales Alpine.js al cargar la página */
    [x-cloak] { display: none !important; }

    /* ========== PREMIUM TESORERIA DESIGN SYSTEM ========== */

    /* Tarjetas de Resumen Financiero Premium */
    .finance-card {
        position: relative;
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.06);
    }
    .finance-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        border-radius: 1.25rem 0 0 1.25rem;
    }
    .finance-card::after {
        content: '';
        position: absolute;
        top: -50%; right: -30%;
        width: 180px; height: 180px;
        border-radius: 50%;
        opacity: 0.04;
        transition: all 0.5s ease;
    }
    .finance-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
    }
    .finance-card:hover::after { opacity: 0.08; transform: scale(1.2); }

    .finance-card.card-income::before { background: linear-gradient(180deg, #10b981, #059669); }
    .finance-card.card-income::after { background: #10b981; }
    .finance-card.card-expense::before { background: linear-gradient(180deg, #ef4444, #dc2626); }
    .finance-card.card-expense::after { background: #ef4444; }
    .finance-card.card-balance::before { background: linear-gradient(180deg, #6366f1, #4f46e5); }
    .finance-card.card-balance::after { background: #6366f1; }

    .finance-card .card-icon {
        width: 44px; height: 44px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    .finance-card:hover .card-icon { transform: scale(1.1) rotate(-5deg); }

    .card-income .card-icon { background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(5,150,105,0.08)); color: #10b981; }
    .card-expense .card-icon { background: linear-gradient(135deg, rgba(239,68,68,0.12), rgba(220,38,38,0.08)); color: #ef4444; }
    .card-balance .card-icon { background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(79,70,229,0.08)); color: #6366f1; }

    .finance-card .card-amount {
        font-size: 1.75rem; font-weight: 800;
        letter-spacing: -0.03em; line-height: 1.1;
        margin: 0.5rem 0 0.25rem;
    }
    .card-income .card-amount { color: #059669; }
    .card-expense .card-amount { color: #dc2626; }
    .card-balance .card-amount { color: #4f46e5; }

    [data-theme='dark'] .finance-card {
        background: linear-gradient(145deg, rgba(30,41,59,0.8), rgba(15,23,42,0.9)) !important;
        border-color: rgba(255,255,255,0.06) !important;
    }
    [data-theme='dark'] .finance-card:hover {
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.4);
        border-color: rgba(255,255,255,0.1) !important;
    }
    [data-theme='dark'] .card-income .card-amount { color: #34d399; }
    [data-theme='dark'] .card-expense .card-amount { color: #f87171; }
    [data-theme='dark'] .card-balance .card-amount { color: #818cf8; }
    [data-theme='dark'] .card-income .card-icon { background: rgba(16,185,129,0.15); color: #34d399; }
    [data-theme='dark'] .card-expense .card-icon { background: rgba(239,68,68,0.15); color: #f87171; }
    [data-theme='dark'] .card-balance .card-icon { background: rgba(99,102,241,0.15); color: #818cf8; }

    /* Tab Selector Premium */
    .treasury-tabs {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 1rem;
        padding: 0.35rem;
    }
    [data-theme='dark'] .treasury-tabs {
        background: rgba(15,23,42,0.6) !important;
        border-color: rgba(255,255,255,0.06) !important;
    }
    .tab-pill {
        border-radius: 0.75rem !important;
        padding: 0.45rem 1rem !important;
        font-weight: 700 !important;
        font-size: 0.78rem !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: none !important;
        cursor: pointer;
    }
    .tab-pill:hover:not(.tab-active) {
        background: rgba(0,0,0,0.04) !important;
    }
    [data-theme='dark'] .tab-pill:hover:not(.tab-active) {
        background: rgba(255,255,255,0.05) !important;
    }
    .tab-pill.tab-active {
        background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3) !important;
    }

    /* Botones de Acción Premium */
    .btn-action-treasury {
        border: none !important;
        border-radius: 2rem !important;
        padding: 0.5rem 1.25rem !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        color: white !important;
        position: relative;
        overflow: hidden;
    }
    .btn-action-treasury::before {
        content: '';
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }
    .btn-action-treasury:hover::before { left: 100%; }
    .btn-action-treasury:hover { transform: translateY(-2px) scale(1.03); }
    .btn-action-treasury:active { transform: translateY(0) scale(0.97); }
    .btn-income { background: linear-gradient(135deg, #10b981, #059669) !important; box-shadow: 0 4px 14px rgba(16,185,129,0.25) !important; }
    .btn-income:hover { box-shadow: 0 8px 20px rgba(16,185,129,0.35) !important; }
    .btn-expense { background: linear-gradient(135deg, #ef4444, #dc2626) !important; box-shadow: 0 4px 14px rgba(239,68,68,0.25) !important; }
    .btn-expense:hover { box-shadow: 0 8px 20px rgba(239,68,68,0.35) !important; }
    .btn-transfer { background: linear-gradient(135deg, #6366f1, #4f46e5) !important; box-shadow: 0 4px 14px rgba(99,102,241,0.25) !important; }
    .btn-transfer:hover { box-shadow: 0 8px 20px rgba(99,102,241,0.35) !important; }

    /* Search Bar Premium */
    .search-treasury {
        border-radius: 1rem;
        border: 1px solid rgba(0,0,0,0.06);
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    .search-treasury:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    [data-theme='dark'] .search-treasury {
        background: rgba(15,23,42,0.5) !important;
        border-color: rgba(255,255,255,0.06) !important;
    }
    [data-theme='dark'] .search-treasury:focus-within {
        border-color: #818cf8 !important;
        box-shadow: 0 0 0 3px rgba(129,140,248,0.1) !important;
    }

    /* Ledger Container Premium */
    .ledger-container {
        border-radius: 1.25rem;
        border: 1px solid rgba(0,0,0,0.06);
        overflow: hidden;
    }
    [data-theme='dark'] .ledger-container {
        background: linear-gradient(145deg, rgba(30,41,59,0.6), rgba(15,23,42,0.8)) !important;
        border-color: rgba(255,255,255,0.06) !important;
    }
    .ledger-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background: rgba(248,250,252,0.5);
        backdrop-filter: blur(8px);
    }
    [data-theme='dark'] .ledger-header {
        background: rgba(15,23,42,0.4) !important;
        border-color: rgba(255,255,255,0.04) !important;
    }

    /* Status Badge Indicator */
    .status-indicator {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.15rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.7rem; font-weight: 600;
    }

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

