<table class="table-custom w-full text-left border-collapse">
    <thead>
        <tr class="border-b border-slate-200 dark:border-slate-800">
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">
                <i class="fa-regular fa-calendar mr-1"></i> Fecha
            </th>
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">
                <i class="fa-solid fa-box-archive mr-1"></i> Fondo / Caja
            </th>
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">
                <i class="fa-solid fa-tag mr-1"></i> Categoría
            </th>
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest w-full min-w-[200px]">
                <i class="fa-solid fa-align-left mr-1"></i> Descripción
            </th>
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right whitespace-nowrap">
                <i class="fa-solid fa-coins mr-1"></i> Monto
            </th>
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">
                Estado
            </th>
            <th class="p-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">
                Acciones
            </th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
        @forelse($recentTransactions as $t)
        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
            <td class="p-4 whitespace-nowrap">
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    {{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}
                </span>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                    {{ $t->reference_number ?? 'Sin Ref.' }}
                </span>
            </td>
            <td class="p-4 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-[10px] font-bold border border-indigo-100 dark:border-indigo-500/20">
                    <i class="fa-solid fa-box-archive"></i> {{ $t->account->name ?? 'Caja General' }}
                </span>
            </td>
            <td class="p-4 whitespace-nowrap">
                @php $esIngreso = $t->type == 'income'; @endphp
                @if($esIngreso)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-100 dark:border-emerald-500/20">
                        <i class="fa-solid fa-arrow-up"></i> {{ $t->category->name ?? 'General' }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-[10px] font-bold border border-rose-100 dark:border-rose-500/20">
                        <i class="fa-solid fa-arrow-down"></i> {{ $t->category->name ?? 'General' }}
                    </span>
                @endif
            </td>
            <td class="p-4">
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    {{ $t->description ?? 'Sin descripción' }}
                </span>
                @if($t->user)
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                        <i class="fa-solid fa-user mr-1"></i> {{ $t->user->name }}
                    </span>
                @endif
            </td>
            <td class="p-4 text-right whitespace-nowrap">
                <div class="inline-flex items-center justify-end gap-1.5">
                    <span class="text-sm font-black {{ $esIngreso ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $esIngreso ? '+' : '-' }} Q{{ number_format($t->amount, 2) }}
                    </span>
                    @if(\Illuminate\Support\Str::startsWith($t->reference_number, 'TRF-'))
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold text-[9px] border border-indigo-100 dark:border-indigo-500/20" title="Transferencia entre Cajas">
                            <i class="fa-solid fa-right-left text-[8px]"></i> TRF
                        </span>
                    @endif
                </div>
            </td>
            <td class="p-4 text-center whitespace-nowrap">
                @if($t->status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-[9px] font-black uppercase tracking-widest">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Completado
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[9px] font-black uppercase tracking-widest">
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div> Anulado
                    </span>
                @endif
            </td>
            <td class="p-4 text-center whitespace-nowrap">
                <div class="flex gap-2 justify-center items-center">
                    @if($t->proof_path)
                        <a href="{{ asset('storage/' . $t->proof_path) }}" target="_blank" 
                           class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-500/20 transition-colors flex items-center justify-center" title="Ver Comprobante">
                            <i class="fa-solid fa-paperclip text-xs"></i>
                        </a>
                    @endif
                    <a href="{{ route('tesoreria.edit', $t->id) }}"
                       class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors flex items-center justify-center" title="Editar">
                       <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-10">
                <div class="text-gray-400 flex flex-col items-center justify-center">
                    <i class="fa-solid fa-receipt fa-3x mb-3 block opacity-30"></i>
                    <p class="mb-1 font-semibold text-lg text-slate-700 dark:text-slate-300">No se encontraron movimientos financieros</p>
                    <p class="text-sm mb-0">Intenta con otros criterios de búsqueda.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@if(isset($recentTransactions) && $recentTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $recentTransactions->hasPages())
<div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900/50">
    <div class="w-full">
        {{ $recentTransactions->appends(request()->query())->links() }}
    </div>
</div>
@endif
