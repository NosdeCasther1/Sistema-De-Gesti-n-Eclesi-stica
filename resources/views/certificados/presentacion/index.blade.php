@extends('layouts.app')

@section('title', 'Certificados de Presentación de Niños - AD Rey de Reyes')

@section('header_title', 'Certificados de Presentación')
@section('header_subtitle', 'Registro y Emisión de Certificados de Presentación de Niños')
@section('header_icon')
<i class="fas fa-baby"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-5">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-1">Registro de Presentaciones</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Consulte y administre los certificados de presentación emitidos</p>
        </div>
        <a href="{{ route('presentacion.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-indigo-500/10 transition-all cursor-pointer">
            <i class="fas fa-plus"></i>
            <span>Nueva Presentación</span>
        </a>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-800/50 dark:text-slate-400 font-black border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Niño/a</th>
                        <th class="px-6 py-4">Padres</th>
                        <th class="px-6 py-4">Fecha de Presentación</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificados as $cert)
                    <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $cert->nino_nombre }}</td>
                        <td class="px-6 py-4">
                            <div class="text-slate-700 dark:text-slate-300"><span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider mr-1.5">Padre:</span>{{ $cert->padre ? $cert->padre->nombres . ' ' . $cert->padre->apellidos : 'N/A' }}</div>
                            <div class="text-slate-700 dark:text-slate-300 mt-1"><span class="text-slate-400 dark:text-slate-500 text-[10px] uppercase font-bold tracking-wider mr-1.5">Madre:</span>{{ $cert->madre ? $cert->madre->nombres . ' ' . $cert->madre->apellidos : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $cert->fecha_presentacion->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('presentacion.pdf', $cert->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 font-bold text-xs transition-colors flex items-center gap-1.5">
                                    <i class="fas fa-file-pdf"></i> Imprimir
                                </a>
                                <form action="{{ route('presentacion.destroy', $cert->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 font-bold text-xs transition-colors cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500 font-medium">No hay registros de presentaciones aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $certificados->links() }}
        </div>
    </div>
</div>
@endsection
