<template x-if="confirmModal.open">
    <div class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div @click="confirmModal.open = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
        
        <!-- Contenedor del Modal -->
        <div 
             @keydown.escape.window="confirmModal.open = false"
             class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800/80 max-w-md w-full overflow-hidden z-10 text-left p-0 group">
            
            <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3 text-base">
                    <div class="config-icon-box icon-box-danger group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <span x-text="confirmModal.title">Confirmación</span>
                </h6>
                <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 border-0 bg-transparent cursor-pointer transition-colors" @click="confirmModal.open = false">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form :action="confirmModal.actionUrl" method="POST" @submit="if ($el.checkValidity()) { isSubmitting = true }" class="m-0">
                @csrf
                <template x-if="confirmModal.method === 'DELETE'">
                    <input type="hidden" name="_method" value="DELETE">
                </template>
                <template x-if="confirmModal.method === 'PUT'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 text-left">
                    <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed mb-0 font-medium" x-html="confirmModal.message"></p>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button type="button" @click="confirmModal.open = false" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">Cancelar</button>
                    
                    <button type="submit" :disabled="isSubmitting" 
                            :class="confirmModal.buttonClass || 'btn-bento-danger'"
                            class="px-6 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed border-0 shadow-lg">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <i class="fas fa-check mr-1"></i>
                            <span x-text="confirmModal.buttonText">Confirmar</span>
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
