<div class="overflow-y-auto flex-grow p-1 md:p-3 custom-scrollbar" style="min-height: 0;">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-6">
        @forelse($miembros as $miembro)
            @php
                // Definición dinámica de colores sutiles por ministerio o rol para evitar saturación
                $roleColor = match(true) {
                    $miembro->es_lider => 'border-indigo-500/30 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10',
                    default => 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50'
                };
                $liderLabel = $miembro->es_lider ? 'Líder' : '';
                $mins = $miembro->ministerios->pluck('nombre')->implode(', ');
                $ministerioText = $mins ? ($liderLabel ? $liderLabel . ' / ' . $mins : $mins) : ($liderLabel ?: 'Miembro');
            @endphp

            {{-- TARJETA BENTO PREMIUM --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                
                {{-- CUERPO DE LA TARJETA --}}
                <div class="p-5 space-y-4">
                    
                    {{-- FOTO Y DATOS PRINCIPALES --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-4 overflow-hidden">
                            {{-- Contenedor de Avatar --}}
                            <div class="h-14 w-14 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0 overflow-hidden relative shadow-inner">
                                @if($miembro->foto && $miembro->foto !== 'default_avatar.png')
                                    <img src="{{ asset('storage/miembros/' . $miembro->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-lg font-black text-slate-500 dark:text-slate-400">
                                        {{ strtoupper(substr($miembro->nombres, 0, 1) . substr($miembro->apellidos ?? '', 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="overflow-hidden">
                                <h3 class="text-base font-black text-slate-900 dark:text-white truncate leading-snug group-hover:text-indigo-500 transition-colors">
                                    {{ $miembro->nombres }} {{ $miembro->apellidos }}
                                </h3>
                                <span class="inline-block px-2.5 py-0.5 border rounded text-[9px] font-black uppercase tracking-widest mt-1 {{ $roleColor }}">
                                    {{ $ministerioText }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- ID de Registro Flotante Derecha --}}
                        <span class="text-[9px] font-mono font-bold text-slate-500 dark:text-slate-400 bg-slate-100/40 dark:bg-slate-950/60 px-2 py-1 rounded border border-slate-200 dark:border-slate-800/60 shrink-0">
                            ID: #{{ str_pad($miembro->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    {{-- INSIGNIAS DE ESTADO (ACTIVO / BAUTIZADO) --}}
                    <div class="flex flex-wrap gap-2 pt-1">
                        @if($miembro->estado)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[9px] font-black uppercase tracking-wider">
                                <div class="w-1 h-1 rounded-full bg-emerald-500"></div> Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-[9px] font-black uppercase tracking-wider">
                                <div class="w-1 h-1 rounded-full bg-rose-500"></div> Inactivo
                            </span>
                        @endif

                        @if($miembro->etapa_consolidacion)
                            @php
                                $isBautizado = strtolower($miembro->etapa_consolidacion) === 'bautizado';
                                $etapaIcon = $isBautizado ? 'fa-solid fa-water' : 'fa-solid fa-star';
                                $etapaColor = $isBautizado 
                                    ? 'bg-sky-50 dark:bg-sky-500/10 border-sky-200 dark:border-sky-500/20 text-sky-700 dark:text-sky-400' 
                                    : 'bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20 text-amber-700 dark:text-amber-400';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md border text-[9px] font-black uppercase tracking-wider {{ $etapaColor }}">
                                <i class="{{ $etapaIcon }} text-[8px]"></i> {{ $miembro->etapa_consolidacion }}
                            </span>
                        @endif
                    </div>

                    {{-- BLOQUE DE DATOS DE CONTACTO (Jerarquía Tipográfica Mejorada) --}}
                    <div class="bg-slate-100/40 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 rounded-xl p-3.5 space-y-2.5 shadow-inner">
                        <div class="flex items-center text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-id-card text-slate-400 dark:text-slate-600 text-xs w-5"></i>
                            <span class="text-xs font-mono font-bold tracking-wide">{{ $miembro->dpi ?? 'No registrado' }}</span>
                        </div>
                        <div class="flex items-center text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-phone text-slate-400 dark:text-slate-600 text-xs w-5"></i>
                            <span class="text-xs font-bold">{{ $miembro->telefono ?? '--- ---' }}</span>
                        </div>
                        <div class="flex items-center text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-house-chimney text-slate-400 dark:text-slate-600 text-xs w-5"></i>
                            <span class="text-xs font-bold truncate">Familia: {{ $miembro->familia->nombre ?? 'Sin Asignar' }}</span>
                        </div>
                    </div>

                </div>

                {{-- BOTONES DE ACCIÓN INFERIOR (Robustecidos) --}}
                <div class="grid grid-cols-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 divide-x divide-slate-100 dark:divide-slate-800">
                    <a href="{{ route('miembros.carnet', $miembro->id) }}" target="_blank"
                       class="py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-address-card text-xs text-slate-400 group-hover:text-indigo-500"></i> Carnet
                    </a>
                    <a href="{{ route('miembros.show', $miembro->id) }}"
                       class="py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-user-gear text-xs text-slate-400"></i> Perfil
                    </a>
                    <a href="{{ route('miembros.edit', $miembro->id) }}"
                       class="py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square text-xs text-slate-400"></i> Editar
                    </a>
                    <form action="{{ route('miembros.destroy', $miembro->id) }}" method="POST" class="m-0 p-0 flex" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este miembro?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-600 dark:hover:text-rose-400 transition-all flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-trash-can text-xs text-slate-400 group-hover:text-rose-500 transition-colors"></i> Eliminar
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full py-20 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 mb-4 shadow-inner">
                    <i class="fas fa-users text-4xl"></i>
                </div>
                <h3 class="text-lg font-black text-slate-700 dark:text-slate-300 mb-1">No se encontraron miembros</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Intenta con otros criterios de búsqueda o agrega un nuevo registro.</p>
            </div>
        @endforelse
    </div>
</div>

@if($miembros->hasPages())
<div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-b-3xl">
    <div class="w-full tailwind-pagination">
        {{ $miembros->appends(request()->query())->links() }}
    </div>
</div>
@endif

<style>
/* Estilos para el scrollbar de las cards (ocultar el feo scroll por defecto si se puede) */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(71, 85, 105, 0.4); }
</style>
