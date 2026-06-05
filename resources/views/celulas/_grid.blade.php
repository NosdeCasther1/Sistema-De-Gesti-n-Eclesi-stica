<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6" id="celulasGrid">
    @forelse($celulas as $celula)
        {{-- TARJETA COMPACTA --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
            
            <div class="p-5">
                {{-- Cabecera: Nombre y Zona --}}
                <div class="flex justify-between items-start gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shrink-0">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight group-hover:text-indigo-500 transition-colors">{{ $celula->nombre }}</h3>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-0.5">
                                <i class="fa-regular fa-clock mr-1"></i> {{ $celula->dia_reunion }} - {{ \Carbon\Carbon::parse($celula->hora_reunion)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 text-[9px] font-black uppercase tracking-widest shrink-0">
                        <i class="fa-solid fa-location-dot"></i> {{ $celula->sector ?? 'Sin Sector' }}
                    </span>
                </div>

                {{-- Líder Responsable (CORRECCIÓN DE CONTRASTE AQUÍ, evitamos bg-slate-50) --}}
                <div class="bg-slate-100/40 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/60 rounded-xl p-3 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-black text-xs shrink-0 shadow-sm">
                        {{ strtoupper(substr($celula->lider->nombres ?? 'S', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Líder Responsable</p>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $celula->lider->nombres ?? 'Sin asignar' }} {{ $celula->lider->apellidos ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Footer: Integrantes y Acciones (evitamos bg-slate-50/50) --}}
            <div class="bg-slate-100/20 dark:bg-slate-950/20 border-t border-slate-100 dark:border-slate-800 p-3 flex justify-between items-center">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 dark:text-slate-400 pl-2">
                    <i class="fa-solid fa-users text-indigo-500/80"></i> {{ $celula->miembros_count ?? 0 }} {{ ($celula->miembros_count ?? 0) == 1 ? 'Integrante' : 'Integrantes' }}
                </span>
                
                <div class="flex gap-1 items-center">
                    <a href="{{ route('celulas.show', $celula->id) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 transition-colors" title="Ver Detalles">
                        <i class="fa-solid fa-eye text-xs"></i>
                    </a>
                    <a href="{{ route('celulas.edit', $celula->id) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 transition-colors" title="Editar Célula">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </a>
                    <form action="{{ route('celulas.destroy', $celula->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('¿Está seguro de eliminar esta célula familiar?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition-colors" title="Eliminar Célula">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-16 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl">
            <i class="fa-solid fa-network-wired text-slate-400 dark:text-slate-600 text-5xl mb-4 opacity-50"></i>
            <h5 class="text-base font-bold text-slate-800 dark:text-slate-300 mb-1">No se encontraron células</h5>
            <p class="text-xs text-slate-400 dark:text-slate-500">Crea una nueva célula para comenzar a organizar a los miembros.</p>
        </div>
    @endforelse
</div>

@if($celulas->hasPages())
<div class="mt-8 flex justify-center">
    <div class="tailwind-pagination">
        {{ $celulas->appends(request()->query())->links() }}
    </div>
</div>
@endif
