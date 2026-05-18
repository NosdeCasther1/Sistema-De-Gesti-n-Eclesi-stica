<template x-if="showModalCaja">
    <div class="fixed inset-0 z-[200] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div @click="showModalCaja = false; document.getElementById('formModalCaja').reset()" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

        <!-- Contenedor del Modal -->
        <div @click.away="showModalCaja = false; document.getElementById('formModalCaja').reset()"
             @keydown.escape.window="showModalCaja = false; document.getElementById('formModalCaja').reset()"
             class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200 dark:border-slate-800/80 z-10 text-left p-0">
            
            <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3 text-base">
                    <div class="rounded-2xl bg-primary/10 p-2.5 text-primary flex items-center justify-center shadow-sm w-11 h-11">
                        <i class="fas fa-box text-lg"></i>
                    </div>
                    <span>Nueva Caja o Fondo</span>
                </h6>
                <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 border-0 bg-transparent cursor-pointer transition-colors" @click="showModalCaja = false; document.getElementById('formModalCaja').reset()">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="formModalCaja" action="{{ route('configuracion.accounts.store') }}" method="POST" @submit="isSubmitting = true" class="m-0">
                @csrf
                <div class="p-6 text-left space-y-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre de la Caja *</label>
                        <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm" placeholder="Ej: Caja General, Ministerio de Niños...">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Saldo Inicial *</label>
                        <input type="number" name="initial_balance" step="0.01" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm font-mono" placeholder="0.00">
                        <div class="mt-2.5 p-3 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs flex items-start gap-2.5 shadow-sm">
                            <i class="fas fa-shield-alt mt-0.5 text-amber-600 dark:text-amber-400 flex-shrink-0 text-sm"></i>
                            <span class="leading-relaxed"><strong>Protección Contable:</strong> Este valor establece el balance inmutable de apertura y no podrá editarse posteriormente.</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Descripción (Opcional)</label>
                        <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm" placeholder="Propósito del fondo..."></textarea>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button type="button" @click="showModalCaja = false; document.getElementById('formModalCaja').reset()" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">Cancelar</button>
                    
                    <button type="submit" :disabled="isSubmitting" 
                            class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-bold shadow-lg shadow-primary/20 flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed border-0">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <i class="fas fa-save mr-1"></i>
                            <span>Guardar Caja</span>
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
