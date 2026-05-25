@extends('layouts.app')

@section('title', 'Editar Familia - AD Rey de Reyes')

@push('styles')
<style>
    /* Bento Card Hover Effects */
    .bento-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bento-card:hover {
        transform: translateY(-4px);
    }

    /* Bento Buttons Premium */
    .btn-bento-primary {
        background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(37,99,235,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
        box-shadow: 0 6px 20px rgba(37,99,235,0.35) !important;
        transform: translateY(-2px);
    }

    /* Icon Boxes Premium */
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
</style>
@endpush

@section('header_title', 'Edición de Familia')
@section('header_subtitle', 'Modifique los datos generales del núcleo familiar')
@section('header_icon')
<i class="fas fa-home fs-5"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-5xl mx-auto">
    <!-- Barra de Navegación / Regreso -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1 flex items-center gap-3">
                <span>Editar Familia: {{ $familia->nombre }}</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Modifique los campos necesarios para mantener el núcleo familiar actualizado</p>
        </div>
        <a href="{{ route('familias.show', $familia->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-sm transition-all no-underline">
            <i class="fas fa-arrow-left text-sm"></i>
            <span>Volver al Perfil</span>
        </a>
    </div>

    <!-- Contenedor Principal Bento -->
    <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
        <!-- Glow de fondo -->
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

        @if($errors->any())
        <div class="mb-8 p-5 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 shadow-sm">
            <div class="font-bold text-xs mb-2 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Por favor corrige los siguientes errores:</span>
            </div>
            <ul class="mb-0 ps-5 text-xs space-y-1 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('familias.update', $familia->id) }}" method="POST" class="m-0 space-y-10">
            @csrf
            @method('PUT')

            {{-- SECCIÓN 1: DATOS DEL NÚCLEO FAMILIAR --}}
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="stat-icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 shadow-sm flex-shrink-0">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight mb-0.5">1. Datos del Núcleo Familiar</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Identificación principal y contacto directo de la familia</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre de la Familia *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $familia->nombre) }}" placeholder="Ej: Familia Pérez García" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Teléfono Principal</label>
                        <input type="text" name="telefono_principal" value="{{ old('telefono_principal', $familia->telefono_principal) }}" placeholder="Ej: 55551234 (8 dígitos)"
                               maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 8)"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Célula Familiar</label>
                        <select name="celula_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                            <option value="">Seleccionar Célula (Opcional)</option>
                            @foreach($celulas as $celula)
                                <option value="{{ $celula->id }}" {{ old('celula_id', $familia->celula_id) == $celula->id ? 'selected' : '' }}>{{ $celula->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Dirección de Domicilio</label>
                        <textarea name="direccion" rows="2" placeholder="Calle, Avenida, Zona, Municipio..."
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm resize-none">{{ old('direccion', $familia->direccion) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Notas / Observaciones</label>
                        <textarea name="notes" rows="2" placeholder="Información adicional relevante sobre la familia..."
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm resize-none">{{ old('notas', $familia->notas) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Botones de Guardado -->
            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('familias.show', $familia->id) }}" class="px-6 py-3.5 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all no-underline cursor-pointer">Cancelar</a>
                <button type="submit" class="btn-bento-primary px-8 py-3.5 rounded-2xl font-bold text-xs flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                    <i class="fas fa-save text-base"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
