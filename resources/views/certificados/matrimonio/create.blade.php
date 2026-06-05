@extends('layouts.app')

@section('title', 'Nuevo Certificado de Matrimonio - AD Rey de Reyes')

@section('header_title', 'Nuevo Matrimonio')
@section('header_subtitle', 'Registro de un nuevo certificado de matrimonio')
@section('header_icon')
<i class="fas fa-ring"></i>
@endsection

@section('content')
<div class="py-8 px-4 max-w-4xl mx-auto">
    <!-- Barra de Navegación / Regreso -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1 flex items-center gap-3">
                <span>Nuevo Matrimonio</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium font-outfit">Registre el matrimonio civil o eclesiástico en la congregación</p>
        </div>
        <a href="{{ route('matrimonio.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-sm transition-all no-underline">
            <i class="fas fa-arrow-left text-sm"></i>
            <span>Volver al listado</span>
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 p-8 relative group">
        <!-- Glow de fondo -->
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all duration-500 pointer-events-none"></div>

        <form action="{{ route('matrimonio.store') }}" method="POST" class="relative z-10 m-0 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Datos de los Cónyuges -->
                <div class="md:col-span-2">
                    <h3 class="text-sm font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2">1. Datos de los Cónyuges</h3>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Esposo *</label>
                    <select name="esposo_id" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm cursor-pointer">
                        <option value="" class="dark:bg-slate-900">Seleccionar Esposo</option>
                        @foreach($miembros as $miembro)
                            @if($miembro->sexo === 'M')
                                <option value="{{ $miembro->id }}" class="dark:bg-slate-900">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Esposa *</label>
                    <select name="esposa_id" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm cursor-pointer">
                        <option value="" class="dark:bg-slate-900">Seleccionar Esposa</option>
                        @foreach($miembros as $miembro)
                            @if($miembro->sexo === 'F')
                                <option value="{{ $miembro->id }}" class="dark:bg-slate-900">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Detalles de la Ceremonia -->
                <div class="md:col-span-2 pt-4">
                    <h3 class="text-sm font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2 border-t border-slate-100 dark:border-slate-800/80 pt-6">2. Detalles de la Ceremonia</h3>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Fecha de Matrimonio *</label>
                    <input type="date" name="fecha_matrimonio" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Pastor Oficiante (Opcional)</label>
                    <input type="text" name="pastor_oficiante" placeholder="Dejar en blanco para usar el Pastor General" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Testigo 1 (Opcional)</label>
                    <input type="text" name="testigo_1" placeholder="Nombre completo del testigo" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Testigo 2 (Opcional)</label>
                    <input type="text" name="testigo_2" placeholder="Nombre completo del testigo" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/80 flex justify-end gap-4">
                <a href="{{ route('matrimonio.index') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-xs bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all no-underline shadow-sm cursor-pointer">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-indigo-500/20 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer">Guardar Matrimonio</button>
            </div>
        </form>
    </div>
</div>
@endsection
