@extends('layouts.app')

@section('title', 'Familias - AD Rey de Reyes')

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
.action-btn.btn-view:hover    { background: rgba(6,182,212,.15);  color: #06b6d4; border-color: #06b6d4; }
.action-btn.btn-edit:hover    { background: rgba(99,102,241,.15); color: #6366f1; border-color: #6366f1; }

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

@section('header_title', 'Gestión de Familias')
@section('header_subtitle', 'Organización, censo y vinculación de núcleos familiares')
@section('header_icon')
<i class="fas fa-home fs-5"></i>
@endsection

@section('content')

<div class="bento-container">
    {{-- ===== ACCIONES ===== --}}
    <div class="flex justify-end mb-4 flex-shrink-0">
        <a href="{{ route('familias.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full font-bold shadow-md flex items-center gap-2 transition-all hover:scale-102 no-underline">
            <i class="fas fa-plus"></i> <span>Nueva Familia</span>
        </a>
    </div>

    {{-- ===== FILTROS ===== --}}
    <div class="card-module p-4 mb-4 shadow-sm flex-shrink-0">
        <form action="{{ route('familias.index') }}" method="GET" id="searchForm" class="max-w-xl m-0">
            <div class="relative w-full">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Búsqueda Rápida</label>
                <div class="relative flex items-center w-full">
                    <span class="absolute left-3.5 text-slate-400 dark:text-slate-500"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" id="searchInput" 
                           class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-xs font-medium"
                           placeholder="Buscar por nombre de familia o dirección..." value="{{ request('search') }}" autocomplete="off">
                    <button type="button" class="absolute right-3 text-slate-400 hover:text-slate-600 clear-search" id="clearSearch" title="Limpiar filtros" style="display: {{ request('search') ? 'flex' : 'none' }}; align-items: center; justify-content: center;">
                        <i class="fas fa-times-circle text-lg"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="card-module p-0 overflow-hidden shadow-sm flex flex-col flex-grow" style="min-height: 0;">
        <div id="table-results" class="results-transition flex flex-col overflow-hidden flex-grow" style="min-height: 0;">
            @include('familias._table')
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const clearBtn = document.getElementById('clearSearch');
        const resultsContainer = document.getElementById('table-results');
        let debounceTimer;

        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });
        }

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
                
                if (searchInput.value.trim() !== '') {
                    clearBtn.style.display = 'flex';
                } else {
                    clearBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                resultsContainer.style.opacity = '1';
            });
        };

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    clearBtn.style.display = 'flex';
                } else {
                    clearBtn.style.display = 'none';
                }
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSearch, 300);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                clearBtn.style.display = 'none';
                performSearch();
            });
        }
        
        // Paginación AJAX
        document.addEventListener('click', function(e) {
            let pagLink = e.target.closest('.tailwind-pagination a') || e.target.closest('.pagination a') || e.target.closest('#table-results nav a');
            if(pagLink) {
                e.preventDefault();
                let url = pagLink.href;
                resultsContainer.style.opacity = '0.5';
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    resultsContainer.innerHTML = html;
                    resultsContainer.style.opacity = '1';
                    window.history.pushState({}, '', url);
                })
                .catch(err => {
                    console.error('Error paginación', err);
                    resultsContainer.style.opacity = '1';
                });
            }
        });
    });
</script>
@endpush
