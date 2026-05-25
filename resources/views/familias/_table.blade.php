<div class="overflow-y-auto flex-grow p-1 md:p-3 custom-scrollbar" style="min-height: 0;">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-6">
        @forelse($familias as $familia)
            {{-- TARJETA BENTO FAMILIA --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                
                <div class="p-5">
                    {{-- CABECERA: ICONO + NOMBRE + BADGE INTEGRANTES --}}
                    <div class="flex items-start gap-4">
                        {{-- Icono sutil y estandarizado --}}
                        <div class="h-12 w-12 rounded-xl border border-indigo-100 dark:border-indigo-500/20 bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-people-roof text-xl text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        
                        <div class="flex-1 overflow-hidden pt-1">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white truncate leading-tight group-hover:text-indigo-500 transition-colors" title="Familia {{ $familia->nombre }}">
                                Familia {{ $familia->nombre }}
                            </h3>
                            
                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-users text-slate-400"></i>
                                    {{ $familia->miembros_count ?? 0 }} {{ ($familia->miembros_count ?? 0) == 1 ? 'Integrante' : 'Integrantes' }}
                                </span>
                                
                                {{-- Badge de Estado (Opcional, si manejas familias activas/inactivas) --}}
                                <span class="inline-flex items-center px-1.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[9px] font-black uppercase tracking-wider">
                                    Activa
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- DATOS DE DIRECCIÓN (Corregido: Sin falso input, evitamos bg-slate-50) --}}
                    <div class="mt-5 bg-slate-100/40 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/60 rounded-xl p-3.5 shadow-inner">
                        <div class="flex items-start gap-3 text-slate-600 dark:text-slate-400">
                            <i class="fa-solid fa-map-location-dot mt-0.5 text-slate-400 dark:text-slate-500"></i>
                            <p class="text-xs font-medium leading-relaxed {{ empty($familia->direccion) ? 'italic text-slate-400' : '' }}" title="{{ $familia->direccion }}">
                                {{ $familia->direccion ?? 'Sin dirección registrada en el sistema.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN (Con Affordance, evitamos bg-slate-50/50) --}}
                <div class="grid grid-cols-2 border-t border-slate-100 dark:border-slate-800 bg-slate-100/20 dark:bg-slate-950/20 divide-x divide-slate-100 dark:divide-slate-800 mt-auto">
                    <a href="{{ route('familias.show', $familia->id) }}"
                       class="py-3.5 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-eye text-sm text-slate-400 group-hover:text-indigo-500 transition-colors"></i> Detalles
                    </a>
                    <a href="{{ route('familias.edit', $familia->id) }}"
                       class="py-3.5 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-sm text-slate-400"></i> Editar
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-full py-20 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 mb-4 shadow-inner">
                    <i class="fas fa-home text-4xl"></i>
                </div>
                <h3 class="text-lg font-black text-slate-700 dark:text-slate-300 mb-1">No se encontraron familias</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Intenta con otros criterios de búsqueda o registra una nueva familia.</p>
            </div>
        @endforelse
    </div>
</div>

@if($familias->hasPages())
<div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-b-3xl">
    <div class="w-full tailwind-pagination">
        {{ $familias->appends(request()->query())->links() }}
    </div>
</div>
@endif

<style>
/* Estilos para el scrollbar de las cards */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(71, 85, 105, 0.4); }
</style>
