@extends('layouts.app')
@section('title', 'Inventario | ' . ($globalConfig->nombre_iglesia ?? 'AD REY DE REYES'))
@section('header_icon') <i class="fas fa-boxes text-xl"></i> @endsection
@section('header_title', 'Gestión de Inventario')
@section('header_subtitle', 'Control de activos de la iglesia')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <form method="GET" action="{{ route('inventario.index') }}" class="w-full md:w-1/2 relative group">
        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
            <i class="fas fa-search text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
        </div>
        <input type="text" name="search" value="{{ $search }}" class="form-control w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm" placeholder="Buscar por nombre, ubicación, responsable...">
    </form>
    
    <a href="{{ route('inventario.create') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl px-6 py-3 font-semibold shadow-md hover:shadow-lg transition-all w-full md:w-auto flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i> Nuevo Artículo
    </a>
</div>

<div class="card-module shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-200 dark:border-slate-700">
                    <th class="px-6 py-4">Artículo</th>
                    <th class="px-6 py-4">Cantidad</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4">Ubicación</th>
                    <th class="px-6 py-4">Responsable</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-sm">
                @forelse($inventarios as $item)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 dark:text-white">{{ $item->nombre }}</div>
                        @if($item->fecha_adquisicion)
                        <div class="text-xs text-gray-500">Adq: {{ \Carbon\Carbon::parse($item->fecha_adquisicion)->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-700 dark:text-slate-300">
                        {{ $item->cantidad }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $badgeColor = match($item->estado) {
                                'Nuevo' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'Bueno' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'Regular' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'Malo' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                            {{ $item->estado }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                        {{ $item->ubicacion ?? 'No asignada' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($item->responsable)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ substr($item->responsable->nombres, 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-700 dark:text-slate-300 truncate max-w-[150px]" title="{{ $item->responsable->nombres }} {{ $item->responsable->apellidos }}">
                                    {{ $item->responsable->nombres }} {{ $item->responsable->apellidos }}
                                </span>
                            </div>
                        @else
                            <span class="text-gray-400 italic">Sin asignar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('inventario.edit', $item) }}" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('inventario.destroy', $item) }}" method="POST" class="inline-block m-0 p-0" onsubmit="return confirm('¿Estás seguro de eliminar este artículo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 text-gray-400 text-2xl">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-1">No hay artículos</h5>
                            <p class="text-gray-500 text-sm max-w-sm mx-auto mb-4">No se encontraron artículos en el inventario que coincidan con tu búsqueda.</p>
                            <a href="{{ route('inventario.create') }}" class="btn text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
                                Agregar el primero
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($inventarios->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800">
        {{ $inventarios->links() }}
    </div>
    @endif
</div>
@endsection
