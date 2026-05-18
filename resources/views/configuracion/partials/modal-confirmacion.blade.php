{{-- MODAL GLOBAL DE CONFIRMACIÓN (TAILWIND CSS + ALPINE.JS) --}}
<template x-if="confirmModal.open">
    <div class="fixed inset-0 z-[300] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-confirmacion-title" role="dialog" aria-modal="true">
        <!-- Overlay con Backdrop Blur -->
        <div @click="confirmModal.open = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
        
        <!-- Contenedor Central del Modal -->
        <div @click.away="confirmModal.open = false"
             @keydown.escape.window="confirmModal.open = false"
             class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800/80 max-w-md w-full overflow-hidden z-10 text-left p-0">
            
            <!-- Encabezado -->
            <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3">
                    <div class="rounded-2xl bg-amber-500/10 p-2.5 text-amber-500 flex items-center justify-center shadow-sm w-11 h-11 flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <span x-text="confirmModal.title" id="modal-confirmacion-title" class="text-base tracking-tight font-bold">Confirmación Requerida</span>
                </h6>
                <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors border-0 bg-transparent cursor-pointer" @click="confirmModal.open = false" title="Cerrar">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Formulario Dinámico -->
            <form :action="confirmModal.actionUrl" method="POST" @submit="isSubmitting = true" class="m-0">
                @csrf
                <!-- Soporte dinámico para métodos HTTP -->
                <template x-if="confirmModal.method === 'DELETE'">
                    <input type="hidden" name="_method" value="DELETE">
                </template>
                <template x-if="confirmModal.method === 'PUT'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Cuerpo del Mensaje -->
                <div class="p-6 text-left space-y-4 bg-white dark:bg-slate-900">
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium mb-0" x-text="confirmModal.message"></p>
                </div>

                <!-- Pie de Acciones -->
                <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button type="button" @click="confirmModal.open = false" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">
                        Cancelar
                    </button>
                    
                    <button type="submit" :disabled="isSubmitting" :class="confirmModal.buttonClass"
                            class="px-6 py-2.5 rounded-xl text-xs font-bold shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed border-0">
                        <template x-if="!isSubmitting">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-check mr-1"></i>
                                <span x-text="confirmModal.buttonText">Confirmar</span>
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Procesando...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
