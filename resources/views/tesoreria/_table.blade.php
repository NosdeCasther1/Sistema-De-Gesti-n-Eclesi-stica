<div class="overflow-x-auto flex-grow" style="overflow-y: auto; min-height: 0;">
    <table class="w-full text-left border-separate border-spacing-0" style="min-width:750px;">
        <thead>
            <tr>
                <th class="pl-4 py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-calendar-alt mr-2 text-blue-500"></i>Fecha</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-box mr-2 text-indigo-500"></i>Fondo / Caja</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-tags mr-2 text-emerald-500"></i>Categoría</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-align-left mr-2 text-amber-500"></i>Descripción</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-coins mr-2 text-rose-500"></i>Monto</th>
                <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;"><i class="fas fa-shield-check mr-2 text-cyan-500"></i>Estado</th>
                <th class="py-3 text-right pr-4 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $t)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-200 dark:border-slate-800/50 last:border-0">
                <td class="pl-4 py-3">
                    <div class="font-semibold text-slate-900 dark:text-white" style="font-size:.92rem;">{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}</div>
                    <div class="text-gray-500 flex items-center gap-1 mt-0.5" style="font-size:.78rem;">
                        <i class="fas fa-hashtag text-slate-400" style="font-size:.7rem;"></i>
                        {{ $t->reference_number ?? 'Sin Ref.' }}
                    </div>
                </td>
                <td class="py-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold text-xs border border-blue-100 dark:border-blue-500/20 shadow-sm">
                        <i class="fas fa-box text-blue-500" style="font-size:0.75-rem;"></i>
                        {{ $t->account->name ?? 'Caja General' }}
                    </span>
                </td>
                <td class="py-3">
                    @php $esIngreso = $t->type == 'income'; @endphp
                    @if($esIngreso)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold text-xs border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                            <i class="fas fa-arrow-up text-emerald-500" style="font-size:0.75rem;"></i>
                            {{ $t->category->name ?? 'General' }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-semibold text-xs border border-rose-100 dark:border-rose-500/20 shadow-sm">
                            <i class="fas fa-arrow-down text-rose-500" style="font-size:0.75rem;"></i>
                            {{ $t->category->name ?? 'General' }}
                        </span>
                    @endif
                </td>
                <td class="py-3">
                    <div class="font-semibold text-slate-900 dark:text-white" style="font-size:.92rem;">{{ $t->description ?? 'Sin descripción' }}</div>
                    @if($t->user)
                    <div class="text-gray-500 flex items-center gap-1 mt-0.5" style="font-size:.78rem;">
                        <i class="fas fa-user text-slate-400" style="font-size:.7rem;"></i>
                        {{ $t->user->name }}
                    </div>
                    @endif
                </td>
                <td class="py-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-base {{ $esIngreso ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $esIngreso ? '+' : '-' }} Q{{ number_format($t->amount, 2) }}
                        </span>
                        @if(\Illuminate\Support\Str::startsWith($t->reference_number, 'TRF-'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold text-xs border border-indigo-100 dark:border-indigo-500/20 shadow-sm" title="Transferencia entre Cajas">
                                <i class="fas fa-exchange-alt" style="font-size:0.7rem;"></i> TRF
                            </span>
                        @endif
                    </div>
                </td>
                <td class="py-3">
                    @if($t->status === 'completed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold text-xs border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Completado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold text-xs border border-slate-200 dark:border-slate-700 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Anulado
                        </span>
                    @endif
                </td>
                <td class="py-3 text-right pr-4">
                    <div class="flex gap-2 justify-end">
                        @if($t->proof_path)
                            <a href="{{ asset('storage/' . $t->proof_path) }}" target="_blank" 
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-500/20 transition-colors shadow-sm" title="Ver Comprobante">
                                <i class="fas fa-paperclip"></i>
                            </a>
                        @endif
                        <a href="{{ route('tesoreria.edit', $t->id) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors shadow-sm" title="Editar">
                            <i class="fas fa-pen text-sm"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-10">
                    <div class="text-gray-400 flex flex-col items-center justify-center">
                        <i class="fas fa-receipt fa-3x mb-3 block opacity-30"></i>
                        <p class="mb-1 font-semibold text-lg text-slate-700 dark:text-slate-300">No se encontraron movimientos financieros</p>
                        <p class="text-sm mb-0">Intenta con otros criterios de búsqueda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($recentTransactions) && $recentTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $recentTransactions->hasPages())
<div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900/50">
    <div class="w-full">
        {{ $recentTransactions->appends(request()->query())->links() }}
    </div>
</div>
@endif

