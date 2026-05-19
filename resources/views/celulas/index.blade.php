@extends('layouts.app')

@section('title', 'Células Familiares - AD Rey de Reyes')

@section('header_title', 'Células Familiares')
@section('header_subtitle', 'Multiplicación y pastoreo sectorizado')
@section('header_icon')
<i class="fas fa-network-wired fs-5"></i>
@endsection

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('celulas.create') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus"></i> <span>Nueva Célula</span>
    </a>
</div>

<div class="card-module p-3 mb-4">
    <form action="{{ route('celulas.index') }}" method="GET" id="searchForm">
        <div class="row g-3">
            <div class="col-md-12">
                <div class="input-group search-bar-premium">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" id="searchInput" class="form-control"
                           placeholder="Buscar por nombre de célula, líder o sector..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
        </div>
    </form>
</div>

<div id="grid-results">
    @include('celulas._grid')
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const resultsContainer = document.getElementById('grid-results');
        let debounceTimer;

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

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performSearch, 300);
        });
    });
</script>
@endpush
@endsection
