<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROYECCIÓN DE PIN - {{ $eleccion->titulo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        html, body { overscroll-behavior: none; }
    </style>
</head>
<body class="bg-[#0b1120] text-slate-100 min-h-screen overflow-hidden flex flex-col antialiased">
    <div x-data="pinProyectorModule('{{ $eleccion->estado === 'activa' && $eleccion->puesto_en_curso ? 'active' : 'waiting' }}', '{{ $eleccion->puesto_en_curso }}', '{{ $eleccion->pin_ronda }}')" x-init="init()" x-cloak class="flex flex-col h-screen p-6 md:p-10 select-none"
         style="background: radial-gradient(ellipse at 50% 40%, #0e0525 0%, #030711 60%, #000000 100%);">
         
        {{-- Grid de puntos decorativo de fondo --}}
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(#a78bfa 1px, transparent 1px); background-size: 40px 40px; z-index: 0; pointer-events: none;"></div>

        {{-- BARRA SUPERIOR: Nombre de la iglesia --}}
        <div class="absolute top-0 left-0 right-0 px-12 py-5 flex items-center justify-between border-b border-white/5 z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-600/30 border border-violet-500/40 flex items-center justify-center">
                    <i class="fa-solid fa-church text-violet-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-white/90 font-black text-sm tracking-wide">AD Rey de Reyes</p>
                    <p class="text-violet-400/70 text-[10px] font-bold uppercase tracking-[0.2em]">Sistema de Votación Electoral</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <template x-if="estado === 'active'">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-emerald-400 font-black text-[11px] uppercase tracking-widest hidden md:inline">Votación en Curso</span>
                    </div>
                </template>
                <button @click="toggleFullscreen()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white/60 hover:text-white rounded-xl font-bold transition-all border border-white/10 flex items-center justify-center group" title="Pantalla Completa">
                    <i class="fa-solid fa-expand text-sm group-hover:scale-110 transition-transform" x-show="!isFullscreen"></i>
                    <i class="fa-solid fa-compress text-sm group-hover:scale-90 transition-transform" x-show="isFullscreen" x-cloak></i>
                </button>
            </div>
        </div>

        {{-- CONTENIDO CENTRAL --}}
        <div class="flex flex-col items-center justify-center h-full pt-20 pb-16 px-8 relative z-10">

            {{-- ESTADO: ESPERANDO RONDA --}}
            <template x-if="estado === 'waiting'">
                <div class="bg-slate-900/50 p-10 md:p-16 rounded-[3rem] border border-slate-800 text-center max-w-2xl mx-auto shadow-2xl backdrop-blur-md">
                    <div class="relative mb-8 w-24 h-24 mx-auto">
                        <div class="absolute inset-0 rounded-full bg-indigo-500 opacity-20 animate-ping"></div>
                        <div class="relative p-6 bg-slate-800 rounded-full border border-slate-700 flex items-center justify-center h-full w-full">
                            <i class="fa-solid fa-hourglass-start text-4xl text-indigo-400 animate-pulse"></i>
                        </div>
                    </div>
                    <h3 class="text-3xl font-black text-white mb-4 tracking-tight">Esperando Apertura de Ronda</h3>
                    <p class="text-lg text-slate-400 leading-relaxed max-w-xl mx-auto">El administrador está preparando la siguiente votación. El PIN de acceso aparecerá automáticamente aquí.</p>
                </div>
            </template>

            {{-- ESTADO: RONDA ACTIVA --}}
            <template x-if="estado === 'active'">
                <div class="flex flex-col items-center w-full max-w-5xl mx-auto">
                    <h3 class="text-violet-300 font-black text-xl md:text-2xl tracking-widest uppercase mb-2" style="color: #c4b5fd; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                        {{ $eleccion->titulo }}
                    </h3>
                    
                    <p class="text-white/60 text-lg md:text-xl font-bold uppercase tracking-[0.3em] mb-2" style="color: rgba(255,255,255,0.8);">Ronda abierta — cargo de</p>
                    <h2 class="font-black uppercase tracking-tight mb-12 leading-none text-white text-center"
                        style="font-size: clamp(3rem, 7vw, 6rem); color: #ffffff; text-shadow: 0 0 40px rgba(167,139,250,0.8);">
                        <span x-text="puestoActual"></span>
                    </h2>

                    {{-- Contenedor del PIN --}}
                    <div class="relative flex flex-col items-center">
                        {{-- Anillo de pulso exterior --}}
                        <div class="absolute inset-[-40px] rounded-[4rem] border border-violet-500/20 animate-ping" style="animation-duration: 3s;"></div>
                        <div class="absolute inset-[-20px] rounded-[3rem] border border-violet-500/15"></div>

                        {{-- Tarjeta del PIN --}}
                        <div class="relative px-12 md:px-24 py-10 md:py-16 rounded-[2.5rem] md:rounded-[3.5rem] border-2 flex flex-col items-center w-full max-w-4xl"
                             style="background: linear-gradient(135deg, #1a0b3d 0%, #0d0a2a 50%, #080618 100%); border-color: rgba(139,92,246,0.5); box-shadow: 0 0 80px rgba(139,92,246,0.25), 0 0 160px rgba(139,92,246,0.1), inset 0 1px 0 rgba(255,255,255,0.05);">

                            <span class="text-xs md:text-sm font-black text-violet-400/80 uppercase tracking-[0.5em] mb-6 flex items-center gap-2">
                                <i class="fa-solid fa-key"></i> PIN DE ACCESO
                            </span>

                            {{-- EL PIN GIGANTE --}}
                            <div class="flex items-center justify-center gap-4 font-mono font-black leading-none whitespace-nowrap"
                                 style="font-size: clamp(5rem, 15vw, 13rem); color: #ede9fe; letter-spacing: 0.15em; text-shadow: 0 0 40px rgba(196,181,253,0.8), 0 0 80px rgba(167,139,250,0.5), 0 0 120px rgba(139,92,246,0.3);"
                                 x-text="pinActual">
                            </div>

                            <span class="text-violet-400/50 text-xs md:text-sm font-bold uppercase tracking-[0.3em] mt-8 text-center px-4">Ingresa este código en tu celular</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        {{-- Footer flotante con instrucciones para votantes (Solo si está activo) --}}
        <template x-if="estado === 'active'">
            <div class="absolute bottom-8 left-0 right-0 flex justify-center z-20 px-4">
                <div class="flex flex-wrap items-center justify-center gap-6 md:gap-10 text-sm md:text-base font-bold bg-slate-900/80 backdrop-blur-md px-6 md:px-10 py-4 rounded-3xl border border-slate-700/50 shadow-2xl">
                    <div class="flex items-center gap-3 text-slate-300">
                        <span class="w-8 h-8 rounded-full bg-violet-600/50 border border-violet-500/50 flex items-center justify-center text-white text-sm font-black shadow-md">1</span>
                        <span>Ingresa al Portal del Votante</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-600 text-sm hidden md:inline"></i>
                    <div class="flex items-center gap-3 text-slate-300">
                        <span class="w-8 h-8 rounded-full bg-violet-600/50 border border-violet-500/50 flex items-center justify-center text-white text-sm font-black shadow-md">2</span>
                        <span>Usa el PIN para votar</span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pinProyectorModule', (initialState, initialPuesto, initialPin) => ({
                estado: initialState,
                puestoActual: initialPuesto,
                pinActual: initialPin,
                pollingInterval: null,
                isFullscreen: false,

                init() {
                    this.pollingInterval = setInterval(() => this.fetchData(), 2500); // Polling cada 2.5s
                    
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });
                },

                toggleFullscreen() {
                    let docElm = document.documentElement;
                    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                        if (docElm.requestFullscreen) {
                            docElm.requestFullscreen();
                        } else if (docElm.mozRequestFullScreen) {
                            docElm.mozRequestFullScreen();
                        } else if (docElm.webkitRequestFullScreen) {
                            docElm.webkitRequestFullScreen();
                        } else if (docElm.msRequestFullscreen) {
                            docElm.msRequestFullscreen();
                        }
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen();
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                    }
                },

                async fetchData() {
                    try {
                        const response = await fetch('{{ route("elecciones.live.data", $eleccion->id) }}');
                        if (!response.ok) return;
                        const data = await response.json();

                        if (this.estado !== 'waiting' && data.status === 'waiting') {
                            window.location.reload();
                        } else if (this.estado !== 'active' && data.status === 'active') {
                             window.location.reload(); // Recarga para obtener el nuevo PIN por Blade
                        } else if (this.puestoActual.toUpperCase() !== data.puesto.toUpperCase()) {
                             window.location.reload(); 
                        }
                    } catch (error) {
                        console.log('Esperando reconexión de red...');
                    }
                }
            }));
        });
    </script>
</body>
</html>
