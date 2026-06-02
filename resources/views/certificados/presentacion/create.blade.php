@extends('layouts.app')

@section('title', 'Nueva Presentación de Niño - AD Rey de Reyes')

@section('header_title', 'Nueva Presentación')
@section('header_subtitle', 'Registro de una nueva presentación de niño(a)')
@section('header_icon')
<i class="fas fa-baby"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800 p-8">
        <form action="{{ route('presentacion.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Datos del Niño -->
                <div class="md:col-span-2">
                    <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-4">1. Datos del Niño / Niña</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Nombre Completo del Niño/a *</label>
                    <input type="text" name="nino_nombre" required class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Fecha de Nacimiento</label>
                    <input type="date" name="nino_fecha_nacimiento" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Lugar de Nacimiento</label>
                    <input type="text" name="lugar_nacimiento" placeholder="Ej: Guatemala" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Datos de los Padres -->
                <div class="md:col-span-2 pt-4">
                    <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-4 border-t border-slate-100 pt-6">2. Datos de los Padres</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Padre (Si es miembro)</label>
                    <select name="padre_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar Padre</option>
                        @foreach($miembros as $miembro)
                            @if($miembro->sexo === 'M')
                                <option value="{{ $miembro->id }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Madre (Si es miembro)</label>
                    <select name="madre_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar Madre</option>
                        @foreach($miembros as $miembro)
                            @if($miembro->sexo === 'F')
                                <option value="{{ $miembro->id }}">{{ $miembro->nombres }} {{ $miembro->apellidos }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Detalles de la Presentación -->
                <div class="md:col-span-2 pt-4">
                    <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest mb-4 border-t border-slate-100 pt-6">3. Detalles de la Presentación</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Fecha de Presentación *</label>
                    <input type="date" name="fecha_presentacion" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Pastor Oficiante (Opcional)</label>
                    <input type="text" name="pastor_oficiante" placeholder="Dejar en blanco para usar el Pastor General" class="w-full rounded-xl border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-4">
                <a href="{{ route('presentacion.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200 transition-all">Guardar Presentación</button>
            </div>
        </form>
    </div>
</div>
@endsection
