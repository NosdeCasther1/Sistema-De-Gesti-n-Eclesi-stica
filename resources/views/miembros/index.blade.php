@extends('layouts.app')

@section('title', 'Listado de Miembros - AD Rey de Reyes')

@push('styles')
<style>
.action-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .8rem;
    border: 1px solid var(--border-color);
    background: var(--bg-card);
    color: var(--text-muted);
    transition: all 0.2s;
    text-decoration: none;
}
.action-btn:hover { transform: translateY(-1px); box-shadow: var(--shadow-sm); }
.action-btn.btn-carnet:hover  { background: rgba(245,158,11,.15); color: #f59e0b; border-color: #f59e0b; }
.action-btn.btn-view:hover    { background: rgba(6,182,212,.15);  color: #06b6d4; border-color: #06b6d4; }
.action-btn.btn-edit:hover    { background: rgba(99,102,241,.15); color: #6366f1; border-color: #6366f1; }

.member-avatar {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .9rem;
    flex-shrink: 0;
}

.results-transition {
    transition: opacity 0.2s ease;
}

/* Forzar arquitectura Bento estricta: Sin scroll en ventana, solo en módulos */
body {
    overflow: hidden !important;
}
.main-content {
    height: 100vh !important;
    max-height: 100vh !important;
    overflow: hidden !important;
}
main {
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    padding-bottom: 0 !important;
}
.bento-container {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    min-height: 0;
}
</style>
@endpush

@section('header_title', 'Listado de Miembros')
@section('header_subtitle', 'Gestión integral de la congregación')
@section('header_icon')
<i class="fas fa-users fs-5"></i>
@endsection

@section('content')

<div class="bento-container">
{{-- ===== ACCIONES ===== --}}
<div class="flex justify-end mb-4 flex-shrink-0">
    <div class="flex gap-2">
        <a href="{{ route('reportes.membresia', array_merge(request()->all(), ['tipo' => 'general'])) }}" target="_blank"
           class="border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 px-4 py-2 rounded-full font-bold shadow-sm flex items-center gap-2 transition-colors">
            <i class="fas fa-file-pdf"></i><span class="hidden md:inline">Reporte PDF</span>
        </a>
        <a href="{{ route('miembros.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full font-bold shadow-sm flex items-center gap-2 transition-colors">
            <i class="fas fa-user-plus"></i> Nuevo Miembro
        </a>
    </div>
</div>

{{-- ===== FILTROS ===== --}}
<div class="card-module p-6 mb-5 shadow-xl flex-shrink-0 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl relative overflow-hidden group">
    <!-- Glow de fondo sutil -->
    <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <form action="{{ route('miembros.index') }}" method="GET" id="searchForm" class="relative z-10 m-0">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
            <div class="md:col-span-3 relative">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Búsqueda Rápida</label>
                <div class="relative flex items-center w-full">
                    <span class="absolute left-4 text-slate-400 dark:text-slate-500"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" id="searchInput" class="w-full pl-11 pr-11 py-3.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                           placeholder="Nombre, DPI..." value="{{ $search }}" autocomplete="off">
                    <button type="button" class="absolute right-3 text-slate-400 hover:text-rose-500 clear-search transition-colors" id="clearSearch" title="Limpiar filtros" style="display: {{ ($search || $ministerio || $etapa || request('cargo')) ? 'flex' : 'none' }}; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Cargo / Liderazgo</label>
                <div class="relative flex items-center w-full">
                    <span class="absolute left-4 text-slate-400 dark:text-slate-500"><i class="fas fa-user-tie"></i></span>
                    <select name="cargo" id="cargoInput" class="w-full pl-11 pr-10 py-3.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none">
                        <option value="">Todos</option>
                        @foreach($cargos as $c)
                            <option value="{{ $c }}" {{ request('cargo') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Ministerio</label>
                <select name="ministerio" class="w-full pl-4 pr-10 py-3.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none" id="ministerioSelect">
                    <option value="">Todos</option>
                    @foreach($ministerios as $m)
                        <option value="{{ $m->id }}" {{ request('ministerio') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Consolidación</label>
                <select name="etapa" class="w-full pl-4 pr-10 py-3.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none" id="etapaSelect">
                    <option value="">Todas</option>
                    @foreach($etapas as $e)
                        <option value="{{ $e }}" {{ $etapa == $e ? 'selected' : '' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

{{-- ===== BENTO GRID ===== --}}
<div class="flex-grow flex flex-col relative overflow-hidden" style="min-height: 0;">
    <div id="table-results" class="results-transition flex flex-col flex-grow h-full overflow-hidden" style="min-height: 0;">
        @include('miembros._table')
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput   = document.getElementById('searchInput');
    const clearBtn      = document.getElementById('clearSearch');
    const searchForm    = document.getElementById('searchForm');
    const resultsBox    = document.getElementById('table-results');
    let debounce;

    const toggleClearBtn = () => {
        if (!clearBtn) return;
        const hasText = searchInput && searchInput.value.trim().length > 0;
        const hasCargo = document.getElementById('cargoInput')?.value.trim().length > 0;
        const hasMin = document.getElementById('ministerioSelect')?.value !== '';
        const hasEtapa = document.getElementById('etapaSelect')?.value !== '';
        if (hasText || hasCargo || hasMin || hasEtapa) {
            clearBtn.style.display = 'inline-flex';
        } else {
            clearBtn.style.display = 'none';
        }
    };

    const doSearch = () => {
        const params = new URLSearchParams(new FormData(searchForm)).toString();
        const url    = `${searchForm.action}?${params}`;
        resultsBox.style.opacity = '0.4';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                resultsBox.innerHTML = html;
                resultsBox.style.opacity = '1';
                window.history.replaceState({}, '', url);
                
                // Update Reporte PDF link with current parameters
                const reporteLink = document.querySelector('a[href*="reportes/membresia"]');
                if (reporteLink) {
                    const baseUrl = reporteLink.href.split('?')[0];
                    reporteLink.href = `${baseUrl}?tipo=general&${params}`;
                }
            })
            .catch(() => { resultsBox.style.opacity = '1'; });
    };

    searchInput?.addEventListener('input', () => { 
        toggleClearBtn(); 
        clearTimeout(debounce); 
        debounce = setTimeout(doSearch, 320); 
    });
    document.getElementById('cargoInput')?.addEventListener('change', () => { toggleClearBtn(); doSearch(); });
    document.getElementById('ministerioSelect')?.addEventListener('change', () => { toggleClearBtn(); doSearch(); });
    document.getElementById('etapaSelect')?.addEventListener('change', () => { toggleClearBtn(); doSearch(); });
    clearBtn?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        const cargoInput = document.getElementById('cargoInput');
        if (cargoInput) cargoInput.value = '';
        searchForm.querySelectorAll('select').forEach(s => s.value = '');
        toggleClearBtn();
        doSearch();
    });
});
</script>
@endpush
