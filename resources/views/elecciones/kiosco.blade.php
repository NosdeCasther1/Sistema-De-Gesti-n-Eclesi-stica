<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kiosco de Votación - {{ $organizacion->nombre }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js v3 CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        html, body { overscroll-behavior: none; }
    </style>
</head>
<body class="bg-slate-50 min-h-dvh text-slate-800 font-sans" 
      x-data="kioscoModule('{{ csrf_token() }}', {{ $eleccion->id }})" x-cloak>

    {{-- BARRA SUPERIOR COMPACTA --}}
    <header class="fixed top-0 left-0 right-0 h-12 sm:h-14 bg-white/90 backdrop-blur-md border-b border-slate-200 z-50 flex items-center justify-between px-3 sm:px-6 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-sm shadow">
                <i class="fa-solid fa-check-to-slot"></i>
            </div>
            <div class="leading-none">
                <h1 class="font-bold text-slate-900 text-sm">Módulo Electoral</h1>
                <p class="text-[9px] uppercase tracking-wider text-slate-400 font-bold">{{ $organizacion->nombre }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-bold rounded-full flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Kiosco Activo
            </span>
            <a href="{{ route('organizaciones.index', ['org' => $organizacion->id]) }}" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 text-sm">
                <i class="fa-solid fa-door-open"></i>
            </a>
        </div>
    </header>

    {{-- CONTENEDOR PRINCIPAL --}}
    <main class="pt-14 sm:pt-16 pb-4 px-3 sm:px-6 max-w-2xl mx-auto min-h-dvh flex flex-col relative">
        
        {{-- LOADER OVERLAY --}}
        <template x-if="procesando">
            <div class="fixed inset-0 z-50 bg-slate-50/80 backdrop-blur-sm flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-indigo-600 mb-3"></div>
                <p class="text-indigo-900 font-bold text-sm animate-pulse">Procesando Voto...</p>
            </div>
        </template>

        {{-- PASO 1: SELECCIONAR VOTANTE --}}
        <div x-show="paso === 1" class="flex-1 flex flex-col">
            <div class="text-center py-4 sm:py-6">
                <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3 border-4 border-white shadow-lg">
                    <i class="fa-solid fa-users-viewfinder text-xl"></i>
                </div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900">Identificación de Votante</h2>
                <p class="text-slate-400 mt-1 text-xs">Busca y selecciona al miembro que va a votar.</p>
            </div>

            <div class="relative mb-3">
                <input type="text" x-model="busquedaVotante" placeholder="Buscar por nombre..." 
                       class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-700 font-medium focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10 transition-all text-sm shadow-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex-1 overflow-hidden flex flex-col min-h-0">
                <div class="overflow-y-auto flex-1 divide-y divide-slate-50">
                    @foreach($padronMiembros as $miembro)
                        <button type="button" @click="seleccionarVotante({{ $miembro->id }}, '{{ addslashes($miembro->nombres . ' ' . $miembro->apellidos) }}')"
                                x-show="busquedaVotante === '' || removerAcentos('{{ addslashes($miembro->nombres . ' ' . $miembro->apellidos) }}').includes(removerAcentos(busquedaVotante))"
                                class="w-full flex items-center text-left px-4 py-3 hover:bg-indigo-50/50 transition-colors group active:bg-indigo-100">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                {{ strtoupper(substr($miembro->nombres, 0, 1) . substr($miembro->apellidos, 0, 1)) }}
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors text-sm truncate">{{ $miembro->nombres }} {{ $miembro->apellidos }}</h3>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $miembro->dpi }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-indigo-500 transition-colors text-xs ml-2"></i>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- PASO 2: PAPELETA DE VOTACIÓN --}}
        <template x-if="paso === 2">
            <div class="flex-1 flex flex-col">
                {{-- Encabezado compacto --}}
                <div class="text-center py-3 sm:py-4 border-b border-slate-100 mb-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Papeleta de Votación</p>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 mt-0.5" x-text="nombreVotante"></h3>
                    <p class="text-indigo-500 font-semibold text-[11px] mt-1"><i class="fa-solid fa-lock text-[9px] mr-1"></i> Tu voto es secreto e inmutable</p>
                </div>

                {{-- Grid de candidatos --}}
                <div class="flex-1 overflow-y-auto min-h-0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($eleccion->candidatos as $candidato)
                            <div @click="candidatoSeleccionado = {{ $candidato->id }}"
                                 class="cursor-pointer bg-white rounded-2xl p-3 sm:p-4 shadow-sm border-2 transition-all duration-200 relative group active:scale-95"
                                 x-bind:class="candidatoSeleccionado === {{ $candidato->id }} ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-md' : 'border-slate-100'">
                                
                                {{-- Check mark --}}
                                <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                     x-bind:class="candidatoSeleccionado === {{ $candidato->id }} ? 'border-indigo-500 bg-indigo-500' : 'border-slate-200'">
                                     <i class="fa-solid fa-check text-white text-[8px]" x-show="candidatoSeleccionado === {{ $candidato->id }}"></i>
                                </div>

                                <div class="flex flex-col items-center text-center">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full mb-2 shadow border-2 border-white overflow-hidden bg-slate-100">
                                        @if($candidato->miembro->foto && $candidato->miembro->foto !== 'default_avatar.png')
                                            <img src="{{ asset('storage/miembros/' . $candidato->miembro->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-lg sm:text-xl">
                                                {{ strtoupper(substr($candidato->miembro->nombres, 0, 1) . substr($candidato->miembro->apellidos, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest px-2 py-0.5 bg-slate-100 text-slate-500 rounded">
                                        {{ $candidato->puesto_postulado }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 mt-1 leading-tight">{{ $candidato->miembro->nombres }} {{ $candidato->miembro->apellidos }}</h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Botones fijos abajo --}}
                <div class="pt-3 pb-2 flex gap-3 border-t border-slate-100 mt-3 shrink-0">
                    <button type="button" @click="cancelarVotacion()" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-sm active:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="button" @click="emitirVoto()" 
                            x-bind:disabled="!candidatoSeleccionado"
                            class="flex-[2] py-3 bg-gradient-to-r from-indigo-500 to-indigo-700 text-white font-black rounded-xl shadow-md text-sm disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98] transition-transform flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Emitir Voto
                    </button>
                </div>
            </div>
        </template>

        {{-- PASO 3: ÉXITO --}}
        <template x-if="paso === 3">
            <div class="flex-1 flex items-center justify-center">
                <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl text-center border-2 border-emerald-100 w-full max-w-sm">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-emerald-400 animate-ping opacity-20"></div>
                        <i class="fa-solid fa-check text-4xl relative z-10"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 mb-2">¡Voto Registrado!</h2>
                    <p class="text-slate-500 text-sm">Tu participación ha sido sellada y encriptada.</p>
                    <p class="text-xs font-bold text-emerald-600 mt-4 bg-emerald-50 py-2 rounded-xl">Regresando en <span x-text="countdownReset"></span>s...</p>
                </div>
            </div>
        </template>

        {{-- TOAST ERRORES --}}
        <template x-if="toastVisible">
            <div class="fixed bottom-4 left-4 right-4 z-50 px-4 py-3 rounded-xl shadow-2xl border bg-rose-100 border-rose-200 text-rose-800 flex items-center gap-2 sm:left-auto sm:right-4 sm:w-auto sm:max-w-sm">
                 <i class="fa-solid fa-circle-exclamation"></i>
                <p class="font-bold text-sm flex-1" x-text="toastMensaje"></p>
            </div>
        </template>

    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kioscoModule', (csrfToken, eleccionId) => ({
                paso: 1,
                busquedaVotante: '',
                votanteId: null,
                nombreVotante: '',
                candidatoSeleccionado: null,
                procesando: false,
                toastVisible: false,
                toastMensaje: '',
                countdownReset: 5,
                timerInterval: null,

                removerAcentos(texto) {
                    if (!texto) return '';
                    return texto.toString().normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                },

                mostrarError(mensaje) {
                    this.toastMensaje = mensaje; 
                    this.toastVisible = true;
                    setTimeout(() => this.toastVisible = false, 5000);
                },

                seleccionarVotante(id, nombre) {
                    this.votanteId = id;
                    this.nombreVotante = nombre;
                    this.busquedaVotante = '';
                    this.candidatoSeleccionado = null;
                    this.paso = 2;
                    window.scrollTo(0,0);
                },

                cancelarVotacion() {
                    this.votanteId = null;
                    this.nombreVotante = '';
                    this.candidatoSeleccionado = null;
                    this.paso = 1;
                },

                async emitirVoto() {
                    if(!this.candidatoSeleccionado || !this.votanteId) return;
                    
                    this.procesando = true;
                    try {
                        const response = await fetch('/votos/emitir', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({
                                eleccion_id: eleccionId,
                                candidato_id: this.candidatoSeleccionado,
                                miembro_id: this.votanteId,
                                modalidad: 'asistido'
                            })
                        });

                        const text = await response.text();
                        let result;
                        try { result = JSON.parse(text); } 
                        catch(e) { throw new Error('Error en el servidor.'); }

                        if (!response.ok) throw new Error(result.message || 'Error al emitir el voto.');

                        this.procesando = false;
                        this.paso = 3;
                        this.iniciarReinicio();

                    } catch (error) {
                        this.procesando = false;
                        this.mostrarError(error.message);
                    }
                },

                iniciarReinicio() {
                    this.countdownReset = 5;
                    this.timerInterval = setInterval(() => {
                        this.countdownReset--;
                        if(this.countdownReset <= 0) {
                            clearInterval(this.timerInterval);
                            this.cancelarVotacion();
                        }
                    }, 1000);
                }
            }));
        });
    </script>
</body>
</html>
