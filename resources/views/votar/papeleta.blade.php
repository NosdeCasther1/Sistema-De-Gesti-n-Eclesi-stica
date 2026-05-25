<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-miembro-id" content="{{ $miembroId }}">
    <title>Papeleta - {{ $eleccion->puesto_en_curso }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 antialiased flex justify-center items-center relative">
    
    <form method="POST" action="{{ route('votar.salir') }}" class="absolute top-4 right-4 z-50">
        @csrf
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-slate-800/85 hover:bg-slate-750 hover:text-white border border-slate-700/50 text-slate-300 shadow-lg backdrop-blur-sm transition-all active:scale-95">
            <i class="fa-solid fa-right-from-bracket text-xs text-rose-400"></i>
            <span>Cerrar Cabina</span>
        </button>
    </form>

    <div x-data="papeletaModule()" class="w-full max-w-lg">
        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="flex flex-col items-center gap-2 mb-3">
                <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest">
                    Ronda Abierta
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-extrabold uppercase tracking-wider">
                    <i class="fa-solid fa-sitemap text-[10px]"></i>
                    <span>{{ $eleccion->organizacion->nombre }}</span>
                </span>
            </div>
            <h1 class="text-2xl font-black text-white">Elección de {{ strtoupper($eleccion->puesto_en_curso) }}</h1>
            <p class="text-slate-400 mt-1 text-xs">{{ $eleccion->titulo }}</p>
        </div>

        {{-- Cabina --}}
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-6 shadow-2xl relative transition-all"
             x-bind:class="procesando ? 'opacity-50 pointer-events-none' : ''">
             
            <div x-show="procesando" style="display: none;" class="absolute inset-0 z-40 flex items-center justify-center bg-slate-900/50 rounded-3xl backdrop-blur-sm">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-500"></div>
            </div>

            <div class="space-y-3">
                @foreach($candidatos as $candidato)
                <label class="flex items-center p-4 border rounded-2xl cursor-pointer transition-all duration-200"
                       x-bind:class="candidatoId === {{ $candidato->id }} ? 'bg-indigo-600/20 border-indigo-500 ring-1 ring-indigo-500' : 'bg-slate-800/50 border-slate-700 hover:bg-slate-800'">
                    <input type="radio" x-model.number="candidatoId" value="{{ $candidato->id }}" class="hidden">
                    
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center mr-4 shrink-0 transition-colors"
                         x-bind:class="candidatoId === {{ $candidato->id }} ? 'border-indigo-500 bg-indigo-500' : 'border-slate-600'">
                         <div class="w-2 h-2 rounded-full bg-white" x-show="candidatoId === {{ $candidato->id }}" style="display: none;"></div>
                    </div>

                    @if($candidato->miembro->foto && $candidato->miembro->foto !== 'default_avatar.png')
                        <img src="{{ asset('storage/miembros/' . $candidato->miembro->foto) }}" 
                             alt="{{ $candidato->miembro->nombres }}" 
                             class="w-10 h-10 rounded-full object-cover shrink-0 mr-4 border border-slate-700">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shrink-0 mr-4">
                            {{ strtoupper(substr($candidato->miembro->nombres, 0, 1) . substr($candidato->miembro->apellidos, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-1">
                        <span class="block text-base font-bold text-white">{{ $candidato->miembro->nombres }} {{ $candidato->miembro->apellidos }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            <button @click="confirmarVoto()" 
                    x-bind:disabled="!candidatoId || procesando"
                    class="mt-8 w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95">
                Emitir Voto Confidencial
            </button>
        </div>

        {{-- MODAL DE CONFIRMACIÓN (DISEÑO PREMIUM EN ESPAÑOL) --}}
        <template x-if="mostrarModalConfirmar">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-slate-800 border border-slate-700 rounded-3xl p-6 w-full max-w-sm shadow-2xl text-center animate-fade-in">
                    <div class="w-16 h-16 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-question text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">¿Confirmar tu Voto?</h3>
                    <p class="text-sm text-slate-300 mb-6">Tu voto es totalmente secreto, anónimo e irreversible. Una vez emitido no se puede modificar.</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="mostrarModalConfirmar = false" 
                                class="py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-xl transition-all">
                            Cancelar
                        </button>
                        <button @click="ejecutarVoto()" 
                                class="py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition-all">
                            Sí, Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL DE ÉXITO --}}
        <template x-if="mostrarModalExito">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-slate-800 border border-slate-700 rounded-3xl p-6 w-full max-w-sm shadow-2xl text-center">
                    <div class="w-16 h-16 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">¡Voto Registrado!</h3>
                    <p class="text-sm text-slate-300 mb-6 font-medium">Tu voto ha sido sellado con éxito en la urna digital.</p>
                    <button @click="irAlInicio()" 
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg transition-all">
                        Aceptar
                    </button>
                </div>
            </div>
        </template>

        {{-- MODAL DE ERROR --}}
        <template x-if="mostrarModalError">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div class="bg-slate-800 border border-slate-700 rounded-3xl p-6 w-full max-w-sm shadow-2xl text-center">
                    <div class="w-16 h-16 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-exclamation text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Error al Votar</h3>
                    <p class="text-sm text-slate-300 mb-6" x-text="mensajeError"></p>
                    <button @click="mostrarModalError = false" 
                            class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-xl transition-all">
                        Entendido
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('papeletaModule', () => ({
                candidatoId: null,
                procesando: false,
                mostrarModalConfirmar: false,
                mostrarModalExito: false,
                mostrarModalError: false,
                mensajeError: '',
                
                confirmarVoto() {
                    if (!this.candidatoId || this.procesando) return;
                    this.mostrarModalConfirmar = true;
                },

                async ejecutarVoto() {
                    this.mostrarModalConfirmar = false;
                    this.procesando = true;
                    try {
                        const metaUser = document.head.querySelector('meta[name="user-miembro-id"]');
                        const response = await fetch('/votos/emitir', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                eleccion_id: {{ $eleccion->id }},
                                candidato_id: this.candidatoId,
                                miembro_id: metaUser ? metaUser.content : null,
                                modalidad: 'digital'
                            })
                        });

                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message || 'Error al emitir voto');
                        
                        this.mostrarModalExito = true;
                    } catch (error) {
                        this.mensajeError = error.message;
                        this.mostrarModalError = true;
                        this.procesando = false;
                    }
                },

                irAlInicio() {
                    window.location.href = '/votar';
                }
            }));
        });
    </script>
</body>
</html>
