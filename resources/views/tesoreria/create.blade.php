@extends('layouts.app')

@section('title', 'Registrar Movimiento - AD Rey de Reyes')

@section('content')
<div class="container-fluid py-4">
    <div class="card-module p-4 max-w-2xl mx-auto shadow-lg" style="max-width: 700px; margin: 0 auto; border-top: 5px solid {{ $tipo == 'Ingreso' ? '#10b981' : '#e11d48' }};">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-white mb-0">
                <i class="fas {{ $tipo == 'Ingreso' ? 'fa-plus-circle text-success' : 'fa-minus-circle text-danger' }} me-2"></i>
                Registrar {{ $tipo }}
            </h3>
            <a href="{{ route('tesoreria.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('tesoreria.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <!-- Categoría -->
                <div class="col-md-6">
                    <label class="form-label text-muted fw-bold small text-uppercase">Categoría / Concepto *</label>
                    <select name="categoria_id" id="categoria_id" class="form-select bg-dark border-secondary text-white" required>
                        <option value="">Seleccione...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Monto -->
                <div class="col-md-6">
                    <label class="form-label text-muted fw-bold small text-uppercase">Monto (Q) *</label>
                    <input type="number" step="0.01" name="monto" class="form-control bg-dark border-secondary text-white fs-4 fw-bold text-{{ $tipo == 'Ingreso' ? 'success' : 'danger' }}" placeholder="0.00" required>
                </div>

                <!-- Fecha -->
                <div class="col-md-6">
                    <label class="form-label text-muted fw-bold small text-uppercase">Fecha *</label>
                    <input type="date" name="fecha" class="form-control bg-dark border-secondary text-white" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Método de Pago -->
                <div class="col-md-6">
                    <label class="form-label text-muted fw-bold small text-uppercase">Método de Pago *</label>
                    <select name="metodo_pago" class="form-select bg-dark border-secondary text-white" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta / Otros</option>
                    </select>
                </div>

                <!-- Miembro (Solo si es Diezmo o similar) -->
                <div id="miembro-container" class="col-md-12 d-none">
                    <label class="form-label text-muted fw-bold small text-uppercase">Asignar a Miembro (Opcional)</label>
                    <select name="miembro_id" class="form-select bg-dark border-secondary text-white">
                        <option value="">Buscar miembro...</option>
                        @foreach($miembros as $m)
                            <option value="{{ $m->id }}">#{{ $m->id }} - {{ $m->nombres }} {{ $m->apellidos }}</option>
                        @endforeach
                    </select>
                    <div class="smaller text-muted mt-1">Recomendado para el control de diezmos personales.</div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12">
                    <label class="form-label text-muted fw-bold small text-uppercase">Notas / Descripción</label>
                    <textarea name="descripcion" class="form-control bg-dark border-secondary text-white" rows="2" placeholder="Detalle adicional del movimiento..."></textarea>
                </div>
            </div>

            <div class="mt-5 pt-3 border-top border-secondary border-opacity-10 d-grid">
                <button type="submit" class="btn btn-{{ $tipo == 'Ingreso' ? 'success' : 'danger' }} py-3 fw-bold fs-5 shadow">
                    <i class="fas fa-save me-2"></i> Procesar Registro
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaSelect = document.getElementById('categoria_id');
        const miembroContainer = document.getElementById('miembro-container');

        categoriaSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            // Mostrar selector de miembros si la categoría incluye "Diezmo"
            if (selectedText.toLowerCase().includes('diezmo')) {
                miembroContainer.classList.remove('d-none');
            } else {
                miembroContainer.classList.add('d-none');
            }
        });
    });
</script>
@endpush
