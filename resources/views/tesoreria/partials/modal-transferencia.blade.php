<!-- ==========================================
     MODAL: TRANSFERENCIA ENTRE CAJAS (VIOLETA/ÍNDIGO)
========================================== -->
<div x-cloak 
     x-show="showTransferModal" 
     x-data="{ 
         fromAccountId: '', 
         amount: 0, 
         isSubmittingTransfer: false,
         get maxAmount() {
             if (!this.fromAccountId) return 0;
             const el = document.querySelector(`option[value='${this.fromAccountId}']`);
             return el ? parseFloat(el.dataset.balance) : 0;
         },
         get isOverdrawn() {
             return this.amount > this.maxAmount;
         }
     }"
     class="fixed inset-0 z-[9999] overflow-y-auto" 
     aria-labelledby="modal-transfer-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop con desenfoque -->
    <div x-show="showTransferModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-md"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-md"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         class="fixed inset-0 bg-slate-950/40 backdrop-blur-md"></div>

    <!-- Contenedor de Posicionamiento -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10">
        <!-- Panel Modal -->
        <div x-show="showTransferModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             
             @keydown.escape.window="showTransferModal = false"
             class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-100 dark:border-slate-800 my-8 flex flex-col text-left">
        
            <!-- Header Índigo/Violeta -->
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center bg-white dark:bg-slate-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-sm">
                        <i class="fas fa-exchange-alt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight" id="modal-transfer-title">Transferencia entre Cajas</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-normal">Movimiento atómico de partida doble con auditoría inmutable</p>
                    </div>
                </div>
                <button @click="showTransferModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Formulario -->
            <form action="{{ route('tesoreria.transfer') }}" method="POST" @submit="isSubmittingTransfer = true" class="p-8 mb-0 flex flex-col gap-6 bg-white dark:bg-slate-900">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Caja de Origen -->
                    <div class="flex flex-col gap-2">
                        <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                            <i class="fas fa-box-open text-indigo-500"></i> Caja Origen (Salida)
                        </label>
                        <select name="from_account_id" x-model="fromAccountId" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all shadow-sm" required>
                            <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">Selecciona la caja origen...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" data-balance="{{ $acc->balance }}" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">{{ $acc->name }} (Saldo Actual: Q{{ number_format($acc->balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Caja de Destino -->
                    <div class="flex flex-col gap-2">
                        <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2">
                            <i class="fas fa-box-archive text-indigo-500"></i> Caja Destino (Entrada)
                        </label>
                        <select name="to_account_id" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all shadow-sm" required>
                            <option value="" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">Selecciona la caja destino...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" x-show="fromAccountId != '{{ $acc->id }}'" class="text-slate-900 bg-white dark:text-white dark:bg-slate-900">{{ $acc->name }} (Saldo Actual: Q{{ number_format($acc->balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Monto -->
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2 justify-between">
                            <span class="flex items-center gap-2"><i class="fas fa-circle-dollar-to-slot text-indigo-500"></i> Monto a Transferir</span>
                            <span x-show="fromAccountId" class="text-xs text-slate-500 dark:text-slate-400 font-normal">Disponible: Q<span x-text="maxAmount.toFixed(2)"></span></span>
                        </label>
                        <div class="relative flex items-center rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 dark:focus-within:border-indigo-500 transition-all shadow-sm">
                            <span class="absolute left-5 text-slate-900 dark:text-slate-400 font-black text-xl">Q</span>
                            <input type="number" name="amount" x-model.number="amount" step="0.01" min="0.01" onwheel="return false;" onkeydown="if(event.key === 'ArrowUp' || event.key === 'ArrowDown') event.preventDefault();" class="w-full bg-transparent py-4 pl-14 pr-6 text-right text-3xl font-bold text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-500 focus:outline-none tracking-tight appearance-none" placeholder="0.00" required>
                        </div>
                        <p x-show="isOverdrawn" class="text-red-500 text-xs mt-1 font-bold">⚠️ Fondos insuficientes en la caja de origen seleccionada.</p>
                    </div>

                    <!-- Descripción -->
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-slate-800 dark:text-slate-400 font-bold text-sm flex items-center gap-2 justify-between">
                            <span class="flex items-center gap-2"><i class="fas fa-pen-to-square text-indigo-500"></i> Concepto de la Transferencia</span>
                            <span class="text-xs font-normal text-slate-500 dark:text-slate-500 lowercase">(Opcional)</span>
                        </label>
                        <input type="text" name="description" class="w-full rounded-2xl border border-slate-300 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 px-4 py-3.5 text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all shadow-sm placeholder-slate-500 dark:placeholder-slate-500" placeholder="Ej. Reasignación de fondos para compra de equipo, apoyo a misiones..." autocomplete="off">
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex flex-col sm:flex-row justify-end items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800/80">
                    <button type="button" @click="showTransferModal = false" class="w-full sm:w-auto btn px-5 py-2.5 rounded-xl font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-transparent text-slate-700 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-slate-200 shadow-sm transition-all cursor-pointer">Cancelar</button>
                    
                    <button type="submit" :disabled="isOverdrawn || isSubmittingTransfer" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all border-0 flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed">
                        <template x-if="!isSubmittingTransfer">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-exchange-alt text-lg"></i>
                                <span>Ejecutar Transferencia</span>
                            </span>
                        </template>
                        <template x-if="isSubmittingTransfer">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.96: 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Procesando...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
