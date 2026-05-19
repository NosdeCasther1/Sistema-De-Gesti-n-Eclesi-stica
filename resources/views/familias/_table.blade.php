<div class="overflow-x-auto flex-grow" style="overflow-y: auto; min-height: 0;">
    <table class="w-full text-left border-separate border-spacing-0" style="min-width:650px;">
        <thead>
            <tr>
                <th class="pl-6 py-3.5 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800" 
                    style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.03); background-clip: padding-box;">
                    <span class="flex items-center gap-2"><i class="fas fa-home text-blue-500 text-xs"></i> Familia</span>
                </th>
                <th class="py-3.5 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800" 
                    style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.03); background-clip: padding-box;">
                    <span class="flex items-center gap-2"><i class="fas fa-users text-blue-500 text-xs"></i> Integrantes</span>
                </th>
                <th class="py-3.5 text-right pr-6 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800" 
                    style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.03); background-clip: padding-box;">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($familias as $familia)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors group">
                <td class="pl-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100/60 dark:border-blue-500/20 shadow-sm transition-transform group-hover:scale-105 duration-300">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" style="font-size:.875rem;">
                                {{ $familia->nombre }}
                            </div>
                            <div class="text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mt-0.5" style="font-size:.75rem;">
                                <i class="fas fa-map-marker-alt text-slate-400/80" style="font-size:.7rem;"></i>
                                <span class="truncate max-w-[280px]">{{ $familia->direccion ?? 'Sin dirección registrada' }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="py-3.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold uppercase tracking-wider border border-blue-100/50 dark:border-blue-500/20 shadow-sm">
                        <i class="fas fa-users text-[10px]"></i>
                        {{ $familia->miembros_count }} {{ $familia->miembros_count == 1 ? 'integrante' : 'integrantes' }}
                    </span>
                </td>
                <td class="py-3.5 text-right pr-6">
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
                <td colspan="3" class="text-center py-12">
                    <div class="text-slate-400 dark:text-slate-600 flex flex-col items-center justify-center">
                        <i class="fas fa-home fa-3x mb-3 opacity-30"></i>
                        <p class="mb-1 font-bold text-base text-slate-800 dark:text-slate-300">No se encontraron familias</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Intente con otros criterios de búsqueda o registre una nueva familia.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($familias->hasPages())
<div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900/50 rounded-b-3xl">
    <div class="w-full">
        {{ $familias->appends(request()->query())->links() }}
    </div>
</div>
@endif
