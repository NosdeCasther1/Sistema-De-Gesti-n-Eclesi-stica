@extends('layouts.app')

@section('title', 'Nuevo Certificado de Matrimonio - AD Rey de Reyes')

@section('header_title', 'Nuevo Matrimonio')
@section('header_subtitle', 'Registro de un nuevo certificado de matrimonio')
@section('header_icon')
<i class="fas fa-ring"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 p-8">
        <form action="{{ route('matrimonio.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Datos de los Cónyuges -->
                <div class="md:col-span-2">
                    <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-4">1. Datos de los Cónyuges</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Esposo *</label>
                    <select name="esposo_id" required class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar Esposo</option>
                        @foreach($miembros as $miembro)
                            @if($miembro->sexo === 'M')
                                <option value="{{ $miembro->id }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Esposa *</label>
                    <select name="esposa_id" required class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar Esposa</option>
                        @foreach($miembros as $miembro)
                            @if($miembro->sexo === 'F')
                                <option value="{{ $miembro->id }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Detalles de la Ceremonia -->
                <div class="md:col-span-2 pt-4">
                    <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-4 border-t border-slate-100 pt-6">2. Detalles de la Ceremonia</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Fecha de Matrimonio *</label>
                    <input type="date" name="fecha_matrimonio" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Pastor Oficiante (Opcional)</label>
                    <input type="text" name="pastor_oficiante" placeholder="Dejar en blanco para usar el Pastor General" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Testigo 1 (Opcional)</label>
                    <input type="text" name="testigo_1" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Testigo 2 (Opcional)</label>
                    <input type="text" name="testigo_2" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-4">
                <a href="{{ route('matrimonio.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200 transition-all">Guardar Matrimonio</button>
            </div>
        </form>
    </div>
</div>
@endsection
