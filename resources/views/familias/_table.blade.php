<div class="overflow-x-auto flex-grow" style="overflow-y: auto; min-height: 0;">
    <table class="w-full text-left border-separate border-spacing-0" style="min-width:650px;">
        <thead>
            <tr>
                <th class="pl-4 py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-home mr-2 text-blue-500"></i>Familia</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-users mr-2 text-blue-500"></i>Integrantes</th>
                <th class="py-3 text-right pr-4 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($familias as $familia)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-200 dark:border-slate-800/50 last:border-0">
                <td class="pl-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.15); color:#f59e0b;">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-white" style="font-size:.92rem;">{{ $familia->nombre }}</div>
                            <div class="text-gray-500 flex items-center gap-1" style="font-size:.78rem;">
                                <i class="fas fa-map-marker-alt text-slate-400" style="font-size:.7rem;"></i>
                                {{ $familia->direccion ?? 'Sin dirección registrada' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="py-3">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-semibold">
                        <i class="fas fa-users"></i>
                        {{ $familia->miembros_count }} {{ $familia->miembros_count == 1 ? 'integrante' : 'integrantes' }}
                    </span>
                </td>
                <td class="py-3 text-right pr-4">
                    <div class="flex gap-2 justify-end">
                        <a href="{{ route('familias.show', $familia->id) }}"
                           class="action-btn btn-view" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('familias.edit', $familia->id) }}"
                           class="action-btn btn-edit" title="Editar">
                            <i class="fas fa-pen"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center py-8">
                    <div class="text-slate-500 dark:text-slate-400 flex flex-col items-center justify-center">
                        <i class="fas fa-home fa-3x mb-3 opacity-20"></i>
                        <p class="mb-1 font-semibold text-lg text-slate-700 dark:text-slate-300">No se encontraron familias</p>
                        <p class="text-sm">Intenta con otros criterios de búsqueda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($familias->hasPages())
<div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900/50">
    <div class="w-full">
        {{ $familias->appends(request()->query())->links() }}
    </div>
</div>
@endif
