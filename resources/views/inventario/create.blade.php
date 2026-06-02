@extends('layouts.app')
@section('title', 'Nuevo Artículo | ' . ($globalConfig->nombre_iglesia ?? 'AD REY DE REYES'))
@section('header_icon') <i class="fas fa-box-open text-xl"></i> @endsection
@section('header_title', 'Nuevo Artículo')
@section('header_subtitle', 'Agregar al inventario')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('inventario.index') }}" class="text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-2 font-medium">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="card-module bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 rounded-2xl overflow-hidden">
        <form action="{{ route('inventario.store') }}" method="POST" class="p-6 md:p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Nombre del Artículo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm @error('nombre') border-red-500 @enderror" required placeholder="Ej. Silla plástica blanca, Micrófono Shure...">
                    @error('nombre') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Cantidad -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Cantidad <span class="text-red-500">*</span></label>
                    <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" min="1" class="form-control w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm @error('cantidad') border-red-500 @enderror" required>
                    @error('cantidad') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" class="form-control w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm @error('estado') border-red-500 @enderror" required>
                        <option value="Nuevo" {{ old('estado') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                        <option value="Bueno" {{ old('estado', 'Bueno') == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                        <option value="Regular" {{ old('estado') == 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="Malo" {{ old('estado') == 'Malo' ? 'selected' : '' }}>Malo</option>
                    </select>
                    @error('estado') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Ubicación -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Ubicación</label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" class="form-control w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm @error('ubicacion') border-red-500 @enderror" placeholder="Ej. Almacén principal, Templo...">
                    @error('ubicacion') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Fecha Adquisición -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Fecha de Adquisición</label>
                    <input type="date" name="fecha_adquisicion" value="{{ old('fecha_adquisicion') }}" class="form-control w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm @error('fecha_adquisicion') border-red-500 @enderror">
                    @error('fecha_adquisicion') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Responsable -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Responsable (Opcional)</label>
                    <select name="responsable_id" class="form-control select2 w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 @error('responsable_id') border-red-500 @enderror">
                        <option value="">Seleccione un responsable...</option>
                        @foreach($miembros as $miembro)
                            <option value="{{ $miembro->id }}" {{ old('responsable_id') == $miembro->id ? 'selected' : '' }}>
                                {{ $miembro->nombres }} {{ $miembro->apellidos }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsable_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Descripción o Detalles</label>
                    <textarea name="descripcion" rows="3" class="form-control w-full bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl px-4 py-3 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm @error('descripcion') border-red-500 @enderror" placeholder="Detalles adicionales, marca, color...">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800 flex justify-end gap-3">
                <a href="{{ route('inventario.index') }}" class="btn px-6 py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="btn px-6 py-3 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar Artículo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Buscar responsable...',
            allowClear: true
        });
    });
</script>
@endpush
