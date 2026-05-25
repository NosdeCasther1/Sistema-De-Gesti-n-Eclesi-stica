<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identificación del Votante - AD Rey de Reyes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        body {
            background: radial-gradient(ellipse at top left, #1e1b4b 0%, #0f172a 50%, #1e293b 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .confirm-card {
            background: rgba(16, 185, 129, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.25);
            backdrop-filter: blur(20px);
        }

        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35);
        }

        #qr-reader video {
            border-radius: 1rem;
            width: 100% !important;
        }

        #qr-reader > div:last-child { display: none !important; }

        .tab-active {
            background: rgba(99, 102, 241, 0.25);
            border-color: rgb(99, 102, 241);
            color: white;
        }
        .tab-inactive {
            background: transparent;
            border-color: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up { animation: slideUp 0.35s ease forwards; }

        @keyframes pulse-ring {
            0%   { transform: scale(0.9); opacity: 1; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        .scan-pulse::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(99, 102, 241, 0.5);
            animation: pulse-ring 1.4s ease infinite;
        }
    </style>
</head>
<body class="text-slate-100 antialiased min-h-screen flex items-center justify-center p-4 relative">

    <form method="POST" action="{{ route('votar.salir') }}" class="absolute top-4 right-4 z-50">
        @csrf
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-slate-800/85 hover:bg-slate-750 hover:text-white border border-slate-700/50 text-slate-300 shadow-lg backdrop-blur-sm transition-all active:scale-95">
            <i class="fa-solid fa-right-from-bracket text-xs text-rose-400"></i>
            <span>Cerrar Cabina</span>
        </button>
    </form>

<div x-data="identificacionModule()" class="w-full max-w-md space-y-0" x-cloak>

    {{-- ===================== FASE 1: BÚSQUEDA ===================== --}}
    <div x-show="fase === 'busqueda'" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="relative inline-flex items-center justify-center mb-4 scan-pulse">
                <div class="w-16 h-16 rounded-full bg-indigo-600/20 border border-indigo-500/40 flex items-center justify-center">
                    <i class="fa-solid fa-id-card text-2xl text-indigo-400"></i>
                </div>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Identificación del Votante</h1>
            <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-sitemap text-[10px]"></i>
                <span>{{ $eleccion->organizacion->nombre }}</span>
            </div>
            <p class="text-slate-400 mt-2 text-xs font-medium">
                {{ $eleccion->titulo }} &mdash; <span class="text-indigo-300 font-bold uppercase tracking-wide">{{ $eleccion->puesto_en_curso }}</span>
            </p>
        </div>

        {{-- Card principal --}}
        <div class="glass-card rounded-3xl p-6 shadow-2xl">

            {{-- Mensajes de error de sesión anterior --}}
            @if($errors->any())
            <div class="mb-5 bg-rose-500/10 border border-rose-500/25 text-rose-400 p-4 rounded-2xl text-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg shrink-0 mt-0.5"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            {{-- Error AJAX --}}
            <div x-show="errorBusqueda" style="display:none"
                 class="mb-5 bg-rose-500/10 border border-rose-500/25 text-rose-400 p-4 rounded-2xl text-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg shrink-0 mt-0.5"></i>
                <span x-text="errorBusqueda"></span>
            </div>

            {{-- Pestañas de método --}}
            <div class="grid grid-cols-2 gap-2 mb-6">
                <button type="button" @click="cambiarMetodo('qr')"
                        :class="metodo === 'qr' ? 'tab-active' : 'tab-inactive'"
                        class="flex items-center justify-center gap-2 py-3 rounded-2xl border text-sm font-bold transition-all duration-200">
                    <i class="fa-solid fa-qrcode text-base"></i>
                    Escanear QR
                </button>
                <button type="button" @click="cambiarMetodo('manual')"
                        :class="metodo === 'manual' ? 'tab-active' : 'tab-inactive'"
                        class="flex items-center justify-center gap-2 py-3 rounded-2xl border text-sm font-bold transition-all duration-200">
                    <i class="fa-solid fa-keyboard text-base"></i>
                    Ingresar Manualmente
                </button>
            </div>

            {{-- Panel QR --}}
            <div x-show="metodo === 'qr'">
                <p class="text-slate-400 text-xs text-center mb-4">Apunta la cámara al código QR del carnet del miembro.</p>
                <div id="qr-reader" class="w-full rounded-2xl overflow-hidden bg-slate-900/50 min-h-[220px] flex items-center justify-center border border-slate-700/50">
                    <div x-show="!camaraActiva" class="text-center py-10">
                        <div class="w-10 h-10 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-slate-500 text-xs">Activando cámara...</p>
                    </div>
                </div>
                <div x-show="buscando" style="display:none" class="mt-4 text-center text-indigo-400 text-sm animate-pulse">
                    <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Verificando miembro...
                </div>
            </div>

            {{-- Panel Manual --}}
            <div x-show="metodo === 'manual'">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                            ID de Miembro <span class="text-slate-600 normal-case font-normal">(número en su carnet)</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-hashtag absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                            <input type="text" x-model="inputManual" id="input-manual"
                                   placeholder="Ej: 42 o 1950601561305"
                                   autocomplete="off" inputmode="numeric"
                                   maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 13)"
                                   @keydown.enter.prevent="buscarMiembro()"
                                   class="input-glow block w-full pl-10 pr-4 py-3 bg-slate-900/60 border border-slate-700 rounded-2xl text-white placeholder-slate-600 focus:border-indigo-500 focus:outline-none transition-all text-sm font-medium">
                        </div>
                        <p class="mt-2 text-slate-500 text-xs">También puedes ingresar tu número de DPI.</p>
                    </div>

                    <button type="button" @click="buscarMiembro()"
                            :disabled="buscando || !inputManual.trim()"
                            class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                        <template x-if="buscando">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                        </template>
                        <template x-if="!buscando">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </template>
                        <span x-text="buscando ? 'Buscando...' : 'Buscar Miembro'"></span>
                    </button>
                </div>
            </div>

            {{-- Divider --}}
            <div class="my-5 flex items-center gap-3">
                <div class="flex-1 h-px bg-slate-700/50"></div>
                <span class="text-slate-600 text-xs font-semibold uppercase tracking-wider">o</span>
                <div class="flex-1 h-px bg-slate-700/50"></div>
            </div>

            {{-- Opción voto manual --}}
            <div class="bg-amber-500/8 border border-amber-500/20 rounded-2xl p-4 text-center">
                <i class="fa-solid fa-hand-paper text-amber-400 text-lg mb-2"></i>
                <p class="text-amber-200/80 text-xs font-medium leading-relaxed">
                    ¿No tienes carnet ni ID? Tu voto será registrado de forma <strong>manual</strong> por el administrador de la mesa. Acércate a ellos.
                </p>
            </div>
        </div>
    </div>

    {{-- ===================== FASE 2: CONFIRMACIÓN ===================== --}}
    <div x-show="fase === 'confirmar'" style="display:none"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="relative inline-flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-emerald-600/20 border border-emerald-500/40 flex items-center justify-center">
                    <i class="fa-solid fa-user-check text-2xl text-emerald-400"></i>
                </div>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Confirmar Identidad</h1>
            <p class="text-slate-400 mt-1 text-sm">Verifica que esta es la persona que va a votar</p>
        </div>

        {{-- Tarjeta del miembro --}}
        <div class="confirm-card rounded-3xl p-6 shadow-2xl mb-4">

            {{-- Avatar + datos --}}
            <div class="flex items-center gap-5 mb-6">
                {{-- Foto o iniciales --}}
                <div class="shrink-0">
                    <template x-if="miembro && miembro.foto_url">
                        <img :src="miembro.foto_url" :alt="miembro.nombres"
                             class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-500/40 shadow-lg">
                    </template>
                    <template x-if="!miembro || !miembro.foto_url">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-2xl shadow-lg border-2 border-indigo-500/40">
                            <span x-text="miembro ? miembro.iniciales : '?'"></span>
                        </div>
                    </template>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-1">Miembro Identificado</p>
                    <h2 class="text-xl font-black text-white leading-tight" x-text="miembro ? (miembro.nombres + ' ' + miembro.apellidos) : ''"></h2>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 text-xs bg-slate-800/60 text-slate-300 border border-slate-700/50 rounded-full px-2.5 py-1">
                            <i class="fa-solid fa-id-card text-[10px] text-slate-500"></i>
                            ID: <span x-text="miembro ? miembro.id : ''"></span>
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs bg-slate-800/60 text-slate-300 border border-slate-700/50 rounded-full px-2.5 py-1">
                            <i class="fa-solid fa-fingerprint text-[10px] text-slate-500"></i>
                            DPI: <span x-text="miembro ? miembro.dpi : ''"></span>
                        </span>
                    </div>
                    <span class="inline-flex items-center gap-1 mt-2 text-xs text-indigo-300 font-semibold">
                        <i class="fa-solid fa-church text-[10px]"></i>
                        <span x-text="miembro ? miembro.ministerio : ''"></span>
                    </span>
                </div>
            </div>

            {{-- Pregunta de confirmación --}}
            <div class="bg-slate-900/50 rounded-2xl p-4 border border-slate-700/40 mb-5 text-center">
                <p class="text-sm text-slate-200 font-medium">
                    <i class="fa-solid fa-circle-question text-indigo-400 mr-2"></i>
                    ¿Confirmas que esta persona va a emitir su voto?
                </p>
            </div>

            {{-- Botones de confirmación --}}
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="volver()"
                        class="py-3.5 bg-slate-700/80 hover:bg-slate-600/80 border border-slate-600/50 text-slate-200 font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    No, Volver
                </button>

                {{-- Formulario oculto que se envía al confirmar --}}
                <form method="POST" action="{{ route('votar.procesar-identificacion') }}" id="form-confirmar">
                    @csrf
                    <input type="hidden" name="identificacion" x-bind:value="miembro ? miembro.id : ''">
                    <button type="submit"
                            class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-lg shadow-emerald-900/40 transition-all active:scale-95 text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        Sí, Confirmar
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-slate-600 text-xs">
            <i class="fa-solid fa-shield-halved text-slate-700 mr-1"></i>
            El voto es completamente secreto y anónimo
        </p>
    </div>

    {{-- Footer --}}
    <p class="text-center text-slate-600 text-xs mt-6" x-show="fase === 'busqueda'">
        &copy; {{ date('Y') }} AD Rey de Reyes &mdash; Sufragio Seguro y Confidencial
    </p>

</div>

<script>
    function identificacionModule() {
        return {
            metodo: 'qr',
            fase: 'busqueda',       // 'busqueda' | 'confirmar'
            camaraActiva: false,
            buscando: false,
            errorBusqueda: null,
            inputManual: '',
            miembro: null,          // Datos del miembro encontrado
            scanner: null,
            qrDetectado: false,

            init() {
                this.$nextTick(() => {
                    this.iniciarEscaner();
                });
            },

            cambiarMetodo(m) {
                this.metodo = m;
                this.errorBusqueda = null;
                if (m === 'qr') {
                    this.$nextTick(() => this.iniciarEscaner());
                } else {
                    this.detenerEscaner();
                    this.$nextTick(() => {
                        const el = document.getElementById('input-manual');
                        if (el) el.focus();
                    });
                }
            },

            async iniciarEscaner() {
                if (this.scanner) {
                    try { await this.scanner.stop(); } catch(e) {}
                }
                this.camaraActiva = false;
                this.qrDetectado = false;

                await new Promise(r => setTimeout(r, 300));

                this.scanner = new Html5Qrcode("qr-reader");

                const onSuccess = (decoded) => this.onQrSuccess(decoded);
                const config = { fps: 12, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 };

                try {
                    await this.scanner.start({ facingMode: "environment" }, config, onSuccess, () => {});
                    this.camaraActiva = true;
                } catch(e) {
                    try {
                        await this.scanner.start({ facingMode: "user" }, config, onSuccess, () => {});
                        this.camaraActiva = true;
                    } catch(e2) {
                        document.getElementById('qr-reader').innerHTML = `
                            <div class="text-center py-10 px-4">
                                <i class="fa-solid fa-camera-slash text-slate-600 text-4xl mb-3"></i>
                                <p class="text-slate-500 text-xs">No se pudo acceder a la cámara. Usa el ingreso manual.</p>
                            </div>`;
                    }
                }
            },

            async detenerEscaner() {
                if (this.scanner) {
                    try { await this.scanner.stop(); } catch(e) {}
                    this.camaraActiva = false;
                }
            },

            async onQrSuccess(decodedText) {
                if (this.qrDetectado || this.buscando) return;
                this.qrDetectado = true;

                let valor = null;
                try {
                    const url = new URL(decodedText);
                    const partes = url.pathname.split('/').filter(Boolean);
                    const ultimo = partes[partes.length - 1];
                    if (/^\d+$/.test(ultimo)) valor = ultimo;
                } catch(e) {
                    if (/^\d+$/.test(decodedText.trim())) valor = decodedText.trim();
                }

                if (!valor) { this.qrDetectado = false; return; }

                await this.detenerEscaner();
                this.inputManual = valor;
                await this.buscarMiembro(valor);
            },

            async buscarMiembro(valorOverride) {
                const valor = valorOverride ?? this.inputManual.trim();
                if (!valor) return;

                this.buscando = true;
                this.errorBusqueda = null;
                this.miembro = null;

                try {
                    const res = await fetch(`/votar/buscar-miembro?q=${encodeURIComponent(valor)}`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        this.errorBusqueda = data.error || 'Error al buscar miembro.';
                        this.qrDetectado = false;
                        if (this.metodo === 'qr') this.iniciarEscaner();
                    } else {
                        this.miembro = data;
                        this.fase = 'confirmar';
                    }
                } catch(e) {
                    this.errorBusqueda = 'Error de conexión. Intenta de nuevo.';
                    this.qrDetectado = false;
                } finally {
                    this.buscando = false;
                }
            },

            volver() {
                this.fase = 'busqueda';
                this.miembro = null;
                this.errorBusqueda = null;
                this.inputManual = '';
                this.qrDetectado = false;
                if (this.metodo === 'qr') {
                    this.$nextTick(() => this.iniciarEscaner());
                }
            }
        };
    }
</script>
</body>
</html>
