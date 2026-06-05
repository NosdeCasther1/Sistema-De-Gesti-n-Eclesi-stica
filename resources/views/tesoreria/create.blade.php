@extends('layouts.app')

@section('title', 'Registrar Movimiento - AD Rey de Reyes')

@section('content')
<div class="py-8 px-4 max-w-2xl mx-auto">
    <div class="card-module p-6 md:p-8 shadow-lg rounded-2xl border border-gray-100 dark:border-slate-800" style="border-top: 5px solid {{ $tipo == 'Ingreso' ? '#10b981' : '#e11d48' }};">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-2">
                <i class="fas {{ $tipo == 'Ingreso' ? 'fa-plus-circle text-emerald-500' : 'fa-minus-circle text-rose-500' }}"></i>
                Registrar {{ $tipo }}
            </h3>
            <a href="{{ route('tesoreria.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('tesoreria.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Categoría / Concepto *</label>
                    <select name="categoria_id" id="categoria_id" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm" required>
                        <option value="">Seleccione...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Monto (Q) *</label>
                    <input type="number" step="0.01" name="monto" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-2xl font-bold {{ $tipo == 'Ingreso' ? 'text-emerald-500' : 'text-rose-500' }} focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm" placeholder="0.00" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Fecha *</label>
                    <input type="date" name="fecha" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm" value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Método de Pago *</label>
                    <select name="metodo_pago" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta / Otros</option>
                    </select>
                </div>

                <div id="miembro-container" class="md:col-span-2 hidden">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Asignar a Miembro (Opcional)</label>
                    <select name="miembro_id" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                        <option value="">Buscar miembro...</option>
                        @foreach($miembros as $m)
                            <option value="{{ $m->id }}">#{{ $m->id }} - {{ $m->nombres }} {{ $m->apellidos }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Recomendado para el control de diezmos personales.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Notas / Descripción</label>
                    <textarea name="descripcion" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm" rows="2" placeholder="Detalle adicional del movimiento..."></textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                <button type="submit" class="w-full py-4 rounded-xl font-bold text-lg text-white shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 {{ $tipo == 'Ingreso' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-rose-500 hover:bg-rose-600' }}">
                    <i class="fas fa-save"></i> Procesar Registro
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
            if (selectedText.toLowerCase().includes('diezmo')) {
                miembroContainer.classList.remove('hidden');
            } else {
                miembroContainer.classList.add('hidden');
            }
        });
    });
</script>
@endpush
