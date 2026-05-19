<div class="overflow-x-auto flex-grow" style="overflow-y: auto; min-height: 0;">
    <table class="w-full text-left border-separate border-spacing-0" style="min-width:650px;">
        <thead>
            <tr>
                <th class="pl-4 py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-user mr-2 text-blue-500"></i>Miembro</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-id-card mr-2 text-blue-500"></i>DPI / Teléfono</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-home mr-2 text-blue-500"></i>Familia</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-check-circle mr-2 text-blue-500"></i>Estado</th>
                <th class="py-3 text-right pr-4 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($miembros as $miembro)
            @php
                $colors = ['#3b82f6','#10b981','#f59e0b','#e11d48','#8b5cf6','#06b6d4'];
                $color  = $colors[$miembro->id % count($colors)];
                $initial = strtoupper(substr($miembro->nombres, 0, 1));
            @endphp
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-200 dark:border-slate-800/50 last:border-0">
                <td class="pl-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm" style="background:{{ $color }}22; color:{{ $color }};">
                            {{ $initial }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-white" style="font-size:.92rem;">{{ $miembro->nombres }} {{ $miembro->apellidos }}</div>
                            <div class="text-gray-500 flex items-center gap-1" style="font-size:.78rem;">
                                <i class="fas fa-briefcase text-blue-500 opacity-70" style="font-size:.7rem;"></i>
                                {{ $miembro->ministerio ?? 'Miembro General' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="py-3">
                    <div class="text-slate-900 dark:text-slate-300" style="font-size:.88rem;font-weight:500;font-variant-numeric:tabular-nums;">
                        {{ $miembro->dpi ?? '—' }}
                    </div>
                    <div class="text-gray-500 flex items-center gap-1" style="font-size:.78rem;">
                        <i class="fas fa-phone text-cyan-500 opacity-70" style="font-size:.7rem;"></i>
                        {{ $miembro->telefono ?? 'Sin teléfono' }}
                    </div>
                </td>
                <td class="py-3">
                    @if($miembro->familia)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-medium text-xs border border-indigo-100 dark:border-indigo-500/20">
                        <i class="fas fa-home" style="font-size:0.7rem;"></i>
                        {{ $miembro->familia->nombre }}
                    </span>
                    @else
                    <span class="text-gray-400" style="font-size:.82rem;">—</span>
                    @endif
                </td>
                <td class="py-3">
                    @if($miembro->estado)
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-100/50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Activo
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100/50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Inactivo
                    </span>
                    @endif
                </td>
                <td class="py-3 text-right pr-4">
                    <div class="flex gap-2 justify-end">
                        <a href="{{ route('miembros.carnet', $miembro) }}" target="_blank"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-600 hover:bg-orange-100 transition-colors" title="Generar Carnet PDF">
                            <i class="fas fa-id-card"></i>
                        </a>
                        <a href="{{ route('miembros.show', $miembro) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 hover:bg-blue-100 transition-colors" title="Ver Perfil">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('miembros.edit', $miembro) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Editar">
                            <i class="fas fa-pen text-sm"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-10">
                    <div class="text-gray-400">
                        <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
                        <p class="mb-0 font-medium">No se encontraron miembros</p>
                        <small>Intenta con otros criterios de búsqueda.</small>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($miembros->hasPages())
<div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900/50">
    <div class="w-full">
        {{ $miembros->appends(request()->query())->links() }}
    </div>
</div>
@endif
