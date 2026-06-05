@extends('layouts.app')

@section('title', 'Certificados de Matrimonio - AD Rey de Reyes')

@section('header_title', 'Certificados de Matrimonio')
@section('header_subtitle', 'Registro y Emisión de Certificados de Matrimonio')
@section('header_icon')
<i class="fas fa-ring"></i>
@endsection

@section('content')
<div class="py-8 px-4 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-5">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-1">Registro de Matrimonios</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-medium">Consulte y administre los certificados de matrimonio emitidos</p>
        </div>
        <a href="{{ route('matrimonio.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-indigo-500/10 transition-all cursor-pointer">
            <i class="fas fa-plus"></i>
            <span>Nuevo Matrimonio</span>
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
                        <th class="px-6 py-4">Esposo</th>
                        <th class="px-6 py-4">Esposa</th>
                        <th class="px-6 py-4">Fecha de Matrimonio</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificados as $cert)
                    <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $cert->esposo->nombres }} {{ $cert->esposo->apellidos }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $cert->esposa->nombres }} {{ $cert->esposa->apellidos }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $cert->fecha_matrimonio->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('matrimonio.pdf', $cert->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 font-bold text-xs transition-colors flex items-center gap-1.5">
                                    <i class="fas fa-file-pdf"></i> Imprimir
                                </a>
                                <form action="{{ route('matrimonio.destroy', $cert->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
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
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500 font-medium">No hay registros de matrimonios aún.</td>
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
