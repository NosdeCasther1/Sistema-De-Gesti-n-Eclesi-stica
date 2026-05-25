<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTEO EN VIVO - AD Rey de Reyes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .bar-growth { transition: height 1.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .leader-glow {
            box-shadow: 0 0 50px 10px rgba(99, 102, 241, 0.3);
            border-color: rgba(129, 140, 248, 0.5);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(30, 41, 59, 0.5); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(71, 85, 105, 0.5); border-radius: 10px; }
    </style>
</head>
<body class="bg-[#0b1120] text-slate-100 min-h-screen overflow-hidden flex flex-col antialiased">
    <div x-data="liveResultsModule()" x-init="init()" x-cloak class="flex flex-col h-screen p-6 md:p-10">
        {{-- ENCABEZADO DE PROYECTO --}}
        <div class="flex justify-between items-center mb-10 shrink-0 bg-slate-900/50 p-5 rounded-3xl border border-slate-800 backdrop-blur-sm">
            <div class="flex items-center gap-5">
                <div class="p-3 bg-slate-800 rounded-2xl border border-slate-700">
                    <i class="fa-solid fa-chart-column text-indigo-400 text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tighter uppercase">Escrutinio Digital Live</h1>
                    <p class="text-lg text-slate-400 font-medium">{{ $eleccion->titulo }}</p>
                </div>
            </div>
            <div class="text-right bg-slate-950 p-4 px-6 rounded-2xl border border-slate-800 shadow-inner">
                <span class="block text-5xl font-black text-white tracking-tight" x-text="totalVotos">0</span>
                <span class="text-xs font-bold text-indigo-300 uppercase tracking-widest">Total Votos Emitidos</span>
            </div>
        </div>

        {{-- AREA PRINCIPAL DE CONTENIDO --}}
        <div class="flex-1 flex overflow-hidden">
            {{-- ESTADO: ESPERANDO RONDA --}}
            <div x-show="estado === 'waiting'" x-transition class="flex-1 flex flex-col items-center justify-center bg-slate-900/30 rounded-3xl border-2 border-dashed border-slate-800 p-10 text-center">
                <div class="relative mb-8">
                    <div class="absolute inset-0 rounded-full bg-indigo-500 opacity-20 animate-ping"></div>
                    <div class="relative p-6 bg-slate-800 rounded-full border border-slate-700">
                        <i class="fa-solid fa-hourglass-start text-6xl text-indigo-400 animate-pulse"></i>
                    </div>
                </div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight mb-3">Esperando Apertura de Ronda</h2>
                <p class="text-xl text-slate-500 max-w-xl">El administrador esta preparando la siguiente votacion. Los resultados apareceran automaticamente aqui.</p>
            </div>

            {{-- ESTADO: RONDA ACTIVA (GRAFICO VERTICAL) --}}
            <div x-show="estado === 'active'" x-transition class="flex-1 flex flex-col w-full h-full">
                <div class="mb-8 text-center shrink-0">
                    <span class="inline-block py-1.5 px-4 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-widest mb-3">
                        <i class="fa-solid fa-satellite-dish mr-1.5 animate-pulse"></i> Transmision en Vivo
                    </span>
                    <h2 class="text-6xl font-black text-white tracking-tighter" x-text="'CARGO: ' + puestoActual"></h2>
                </div>

                {{-- CONTENEDOR DE COLUMNAS --}}
                <div class="flex-1 flex flex-nowrap gap-6 justify-center items-end px-4 pb-10 overflow-x-auto custom-scrollbar h-full">
                    <template x-for="(candidato, index) in candidatos" :key="candidato.id">
                        {{-- COLUMNA DE CANDIDATO --}}
                        <div class="flex flex-col items-center flex-none w-64 h-full animate-fade-in-up"
                             x-bind:style="'animation-delay: ' + (index * 150) + 'ms'">
                            {{-- 1. Porcentaje Superior --}}
                            <div class="text-center mb-4 shrink-0">
                                <span class="text-5xl font-black tracking-tighter"
                                      x-bind:class="index === 0 && candidato.votos > 0 ? 'text-indigo-300' : 'text-slate-400'"
                                      x-text="calculaPorcentaje(candidato.votos) + '%'">
                                </span>
                            </div>

                            {{-- 2. Area de la Barra Vertical --}}
                            <div class="w-full flex-1 bg-slate-900/50 rounded-t-3xl rounded-b-xl border border-slate-800 p-3 flex items-end justify-center relative overflow-hidden transition-all duration-500"
                                 x-bind:class="index === 0 && candidato.votos > 0 ? 'leader-glow bg-slate-900/80' : ''">
                                <div class="w-full rounded-t-xl rounded-b-md bar-growth bg-gradient-to-t from-indigo-900 via-indigo-600 to-indigo-400 relative z-10 shadow-lg"
                                     x-bind:style="'height: ' + calculaPorcentaje(candidato.votos) + '%'">
                                    <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(0deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 100% 10px;"></div>
                                </div>

                                <template x-if="index === 0 && candidato.votos > 0">
                                    <i class="fa-solid fa-crown absolute bottom-10 text-[12rem] text-indigo-500/10 z-0"></i>
                                </template>
                            </div>

                            {{-- 3. Info del Candidato Inferior --}}
                            <div class="w-[90%] -mt-8 bg-slate-800 rounded-2xl p-5 text-center border border-slate-700 shadow-2xl relative z-20 transition-all"
                                 x-bind:class="index === 0 && candidato.votos > 0 ? 'border-indigo-500' : ''">
                                <div class="h-14 w-14 rounded-full bg-indigo-600 mx-auto -mt-10 mb-3 flex items-center justify-center text-white font-black text-xl border-4 border-slate-800 shadow-xl"
                                     x-bind:class="index === 0 && candidato.votos > 0 ? 'bg-indigo-500' : 'bg-slate-600'">
                                     <span x-text="obtenerIniciales(candidato.nombre)"></span>
                                </div>
                                <h4 class="text-lg font-bold text-white leading-tight mb-1 truncate" x-text="candidato.nombre"></h4>
                                <div class="flex justify-center items-baseline gap-2 mt-2 bg-slate-950/50 rounded-lg py-1 px-3 border border-slate-700/50">
                                    <span class="text-2xl font-black text-white" x-text="candidato.votos">0</span>
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">votos</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Footer / Branding --}}
        <div class="mt-8 text-center text-slate-600 text-xs shrink-0 border-t border-slate-800/50 pt-4">
            Modulo de Proyeccion Oficial &copy; AD Rey de Reyes. Datos actualizados en tiempo real mediante Smart Polling.
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liveResultsModule', () => ({
                estado: 'waiting',
                puestoActual: '',
                totalVotos: 0,
                candidatos: [],
                pollingInterval: null,

                init() {
                    this.fetchData();
                    this.pollingInterval = setInterval(() => this.fetchData(), 2500);
                },

                async fetchData() {
                    try {
                        const response = await fetch('{{ route("elecciones.live.data", $eleccion->id) }}');
                        if (!response.ok) return;
                        const data = await response.json();

                        if (data.total_votos !== this.totalVotos || data.status !== this.estado || data.puesto !== this.puestoActual) {
                            this.estado = data.status;
                            if (data.status === 'active') {
                                this.puestoActual = data.puesto;
                                this.totalVotos = data.total_votos;
                                this.candidatos = data.candidatos;
                            }
                        }
                    } catch (error) {
                        console.log('Esperando reconexion de red...');
                    }
                },

                calculaPorcentaje(votos) {
                    if (this.totalVotos === 0) return 0;
                    return ((votos / this.totalVotos) * 100).toFixed(1);
                },

                obtenerIniciales(nombreCompleto) {
                    return nombreCompleto
                        .split(' ')
                        .map(n => n[0])
                        .slice(0, 2)
                        .join('')
                        .toUpperCase();
                }
            }));
        });

        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in-up {
                0% { opacity: 0; transform: translateY(20px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fade-in-up 0.6s ease-out forwards;
                opacity: 0;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
