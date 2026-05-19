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
        <a href="{{ route('reportes.miembros') }}" target="_blank"
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
<div class="card-module p-4 mb-4 shadow-sm flex-shrink-0">
    <form action="{{ route('miembros.index') }}" method="GET" id="searchForm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5 relative">
                <label class="block text-sm mb-1 font-bold text-slate-700 dark:text-slate-300">Búsqueda Rápida</label>
                <div class="relative flex items-center w-full">
                    <span class="absolute left-3 text-slate-400"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" id="searchInput" class="w-full pl-10 pr-10 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                           placeholder="Nombre, DPI, Teléfono..." value="{{ $search }}" autocomplete="off">
                    <button type="button" class="absolute right-3 text-slate-400 hover:text-slate-600 clear-search" id="clearSearch" title="Limpiar filtros" style="display: {{ ($search || $ministerio || $etapa) ? 'flex' : 'none' }}; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm mb-1 font-bold text-slate-700 dark:text-slate-300">Ministerio</label>
                <select name="ministerio" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" id="ministerioSelect">
                    <option value="">Todos</option>
                    @foreach($ministerios as $m)
                        <option value="{{ $m }}" {{ $ministerio == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm mb-1 font-bold text-slate-700 dark:text-slate-300">Consolidación</label>
                <select name="etapa" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" id="etapaSelect">
                    <option value="">Todas las Etapas</option>
                    @foreach($etapas as $e)
                        <option value="{{ $e }}" {{ $etapa == $e ? 'selected' : '' }}>{{ $e }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 flex items-center justify-center transition-colors" title="Aplicar filtros">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ===== TABLA ===== --}}
<div class="card-module p-0 overflow-hidden shadow-sm flex flex-col flex-grow" style="min-height: 0;">
    <div id="table-results" class="results-transition flex flex-col overflow-hidden flex-grow" style="min-height: 0;">
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
        const hasMin = document.getElementById('ministerioSelect')?.value !== '';
        const hasEtapa = document.getElementById('etapaSelect')?.value !== '';
        if (hasText || hasMin || hasEtapa) {
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
            })
            .catch(() => { resultsBox.style.opacity = '1'; });
    };

    searchInput?.addEventListener('input', () => { 
        toggleClearBtn(); 
        clearTimeout(debounce); 
        debounce = setTimeout(doSearch, 320); 
    });
    document.getElementById('ministerioSelect')?.addEventListener('change', () => { toggleClearBtn(); doSearch(); });
    document.getElementById('etapaSelect')?.addEventListener('change', () => { toggleClearBtn(); doSearch(); });
    clearBtn?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        searchForm.querySelectorAll('select').forEach(s => s.value = '');
        toggleClearBtn();
        doSearch();
    });
});
</script>
@endpush
