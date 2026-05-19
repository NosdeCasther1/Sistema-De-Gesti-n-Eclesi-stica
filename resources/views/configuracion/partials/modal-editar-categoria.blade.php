@foreach($categorias as $cat)
    <template x-if="showModalEditarCategoria === {{ $cat->id }}">
        <div class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Overlay -->
            <div @click="showModalEditarCategoria = null; document.getElementById('formModalEditarCategoria_{{ $cat->id }}').reset()" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
            
            <!-- Contenedor del Modal -->
            <div @click.away="showModalEditarCategoria = null; document.getElementById('formModalEditarCategoria_{{ $cat->id }}').reset()"
                 @keydown.escape.window="showModalEditarCategoria = null; document.getElementById('formModalEditarCategoria_{{ $cat->id }}').reset()"
                 class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800/80 max-w-md w-full overflow-hidden z-10 text-left p-0 group">
                
                <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3 text-base">
                        <div class="config-icon-box icon-box-warning group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-edit"></i>
                        </div>
                        <span>Editar Categoría</span>
                    </h6>
                    <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 border-0 bg-transparent cursor-pointer transition-colors" @click="showModalEditarCategoria = null; document.getElementById('formModalEditarCategoria_{{ $cat->id }}').reset()">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="formModalEditarCategoria_{{ $cat->id }}" action="{{ route('categorias.update', $cat->id) }}" method="POST" @submit="isSubmitting = true" class="m-0">
                    @csrf
                    @method('PUT')
                    <div class="p-6 text-left space-y-4">
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre de la Categoría *</label>
                            <input type="text" name="nombre" value="{{ $cat->nombre }}" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Tipo de Movimiento *</label>
                            <select name="tipo" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm cursor-pointer">
                                <option value="ingreso" {{ $cat->tipo === 'ingreso' ? 'selected' : '' }}>🟢 Ingreso (Diezmos, Ofrendas, Donaciones)</option>
                                <option value="gasto" {{ $cat->tipo === 'gasto' ? 'selected' : '' }}>🔴 Gasto / Egreso (Servicios, Mantenimiento)</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button type="button" @click="showModalEditarCategoria = null; document.getElementById('formModalEditarCategoria_{{ $cat->id }}').reset()" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">Cancelar</button>
                        
                        <button type="submit" :disabled="isSubmitting" 
                                class="btn-bento-warning px-6 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed border-0">
                            <span x-show="!isSubmitting" class="flex items-center gap-2">
                                <i class="fas fa-save mr-1"></i>
                                <span>Guardar Cambios</span>
                            </span>
                            <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Procesando...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
@endforeach
