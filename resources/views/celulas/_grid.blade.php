<div class="row g-4" id="celulasGrid">
    @forelse($celulas as $c)
    <div class="col-md-4 col-sm-6 col-12">
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-6 shadow-md transition-all relative overflow-hidden group hover:-translate-y-1 hover:shadow-lg flex flex-col justify-between h-full">
            <!-- Glow de fondo -->
            <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

            <div>
                {{-- Badge de Sector --}}
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20">
                        <i class="fas fa-network-wired text-sm"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $c->sector ?? 'Sin Sector' }}
                    </span>
                </div>

                {{-- Título y Horarios --}}
                <div class="mb-4">
                    <h5 class="text-base font-black text-slate-950 dark:text-white tracking-tight mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $c->nombre }}
                    </h5>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider flex items-center gap-1.5 mt-1.5">
                        <i class="far fa-calendar-alt text-blue-500"></i>
                        <span>{{ $c->dia_reunion }} a las {{ \Carbon\Carbon::parse($c->hora_reunion)->format('h:i A') }}</span>
                    </div>
                </div>

                {{-- Tarjeta de Líder --}}
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/60 shadow-inner mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-600 text-white font-black text-sm shadow-sm flex-shrink-0">
                        {{ strtoupper(substr($c->lider->nombres ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest leading-none mb-1">Líder Responsable</div>
                        <div class="text-xs font-bold text-slate-900 dark:text-slate-200 truncate leading-none">
                            {{ $c->lider->nombres ?? 'Sin Líder' }} {{ $c->lider->apellidos ?? '' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer de Tarjeta --}}
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800/60 mt-auto">
                <div>
                    <div class="text-base font-black text-slate-950 dark:text-white tracking-tight leading-none">
                        {{ $c->miembros_count }}
                    </div>
                    <div class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mt-1">
                        {{ $c->miembros_count == 1 ? 'Integrante' : 'Integrantes' }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('celulas.show', $c->id) }}"
                       class="action-btn btn-view" title="Ver Detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('celulas.edit', $c->id) }}"
                       class="action-btn btn-edit" title="Editar Célula">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('celulas.destroy', $c->id) }}" method="POST" class="inline d-inline m-0" onsubmit="return confirm('¿Está seguro de eliminar esta célula familiar?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn text-rose-500 hover:bg-rose-500/10 hover:text-rose-600 hover:border-rose-500/30" title="Eliminar Célula">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-16 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl">
        <i class="fas fa-network-wired text-slate-400 dark:text-slate-600 text-5xl mb-4 opacity-50"></i>
        <h5 class="text-base font-bold text-slate-800 dark:text-slate-300 mb-1">No se encontraron células</h5>
        <p class="text-xs text-slate-400 dark:text-slate-500">Crea una nueva célula para comenzar a organizar a los miembros.</p>
    </div>
    @endforelse
</div>

@if($celulas->hasPages())
<div class="mt-8 flex justify-center">
    <div class="tailwind-pagination">
        {{ $celulas->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endif
