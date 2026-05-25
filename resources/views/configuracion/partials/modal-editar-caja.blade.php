@foreach($accounts as $account)
    <template x-if="showModalEditarCaja === {{ $account->id }}">
        <div class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Overlay -->
            <div @click="showModalEditarCaja = null" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
            
            <!-- Contenedor del Modal -->
            <div 
                 x-data="{
                     originalName: '{{ addslashes($account->name) }}',
                     originalBalance: {{ $account->initial_balance }},
                     currentName: '{{ addslashes($account->name) }}',
                     currentBalance: {{ $account->initial_balance }},
                     justification: '',
                     isModified() {
                         return this.currentName.trim() !== this.originalName || parseFloat(this.currentBalance) !== parseFloat(this.originalBalance);
                     }
                 }"
                 @keydown.escape.window="showModalEditarCaja = null"
                 class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800/80 max-w-md w-full overflow-hidden z-10 text-left p-0 group">
                
                <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3 text-base">
                        <div class="config-icon-box icon-box-success group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-edit"></i>
                        </div>
                        <span>Editar Caja o Fondo Ministerial</span>
                    </h6>
                    <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 border-0 bg-transparent cursor-pointer transition-colors" @click="showModalEditarCaja = null">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="formModalEditarCaja_{{ $account->id }}" action="{{ route('configuracion.accounts.update', $account->id) }}" method="POST" @submit="if ($el.checkValidity()) { isSubmitting = true }" class="m-0">
                    @csrf
                    @method('PUT')
                    <div class="p-6 text-left space-y-4">
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre de la Cuenta *</label>
                            <input type="text" name="name" x-model="currentName" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Saldo Inicial (Q) *</label>
                            <input type="number" step="0.01" name="initial_balance" x-model="currentBalance" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm font-mono">
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium">Nota: El balance actual se recalculará sumando los ingresos y restando los egresos históricos.</div>
                        </div>

                        <!-- Justificación del Ajuste (Se muestra condicionalmente) -->
                        <div x-show="isModified()" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="p-4 bg-amber-500/10 border border-amber-500/20 dark:bg-amber-500/5 dark:border-amber-500/10 rounded-2xl space-y-2">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1 flex items-center gap-1.5">
                                <i class="fas fa-exclamation-triangle"></i> Justificación de Ajuste Requerida *
                            </label>
                            <textarea name="justification" x-model="justification" :required="isModified()" minlength="10" rows="2" 
                                      placeholder="Explica la razón de este cambio de nombre o saldo (mínimo 10 caracteres)..." 
                                      class="w-full rounded-xl border border-amber-300 dark:border-amber-700/50 bg-white dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all shadow-sm"></textarea>
                            <div class="flex justify-between items-center text-[10px] text-amber-600 dark:text-amber-400/80 font-medium">
                                <span>Este cambio se guardará en la bitácora de auditoría.</span>
                                <span x-text="justification.length + ' / 10 carac. mín.'"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Descripción (Opcional)</label>
                            <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm">{{ $account->description }}</textarea>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button type="button" @click="showModalEditarCaja = null" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">Cancelar</button>
                        
                        <button type="submit" :disabled="isSubmitting || (isModified() && justification.trim().length < 10)" 
                                class="btn-bento-success px-6 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed border-0">
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
