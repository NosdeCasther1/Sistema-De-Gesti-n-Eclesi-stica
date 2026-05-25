<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Votante - AD Rey de Reyes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(ellipse at top left, #1e1b4b 0%, #0f172a 50%, #1e293b 100%);
            min-height: 100vh;
        }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
        .input-pin { letter-spacing: 0.35em; font-size: 2.5rem; font-weight: 900; text-align: center; text-transform: uppercase; }
        .input-pin::placeholder { letter-spacing: 0.2em; }
    </style>
</head>
<body class="text-slate-100 antialiased min-h-screen flex items-center justify-center p-4 relative">
    
    @if(session('eleccion_id_activa'))
    <form method="POST" action="{{ route('votar.salir') }}" class="absolute top-4 right-4">
        @csrf
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-slate-800/85 hover:bg-slate-750 hover:text-white border border-slate-700/50 text-slate-300 shadow-lg backdrop-blur-sm transition-all active:scale-95">
            <i class="fa-solid fa-right-from-bracket text-xs text-rose-400"></i>
            <span>Cerrar Cabina</span>
        </button>
    </form>
    @endif

    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600/20 border border-indigo-500/40 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-person-booth text-2xl text-indigo-400"></i>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Voto Electrónico</h1>
            <p class="text-slate-400 mt-1 text-sm">Sistema de Asamblea Ministerial — AD Rey de Reyes</p>
        </div>

        {{-- ======== CASO A: Votante ya identificado ======== --}}
        @if($votante)
        <div class="glass rounded-3xl p-6 shadow-2xl mb-4">

            {{-- Chip de sesión activa --}}
            <div class="flex items-center gap-2 mb-5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Votante Identificado</span>
            </div>

            @if(isset($eleccion) && $eleccion)
            <div class="mb-5 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl p-4 text-center">
                <span class="block text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-1">Elección Activa</span>
                <span class="block text-base font-extrabold text-white leading-tight mb-1">{{ $eleccion->organizacion->nombre }}</span>
                <span class="block text-xs text-indigo-200/80 font-bold uppercase tracking-wider mb-1">{{ $eleccion->puesto_en_curso }}</span>
                <span class="block text-[10px] text-slate-400 font-medium">{{ $eleccion->titulo }}</span>
            </div>
            @endif

            {{-- Tarjeta del miembro --}}
            <div class="flex items-center gap-4 bg-emerald-500/8 border border-emerald-500/20 rounded-2xl p-4 mb-5">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-xl shadow-lg shrink-0">
                    {{ strtoupper(substr($votante->nombres, 0, 1) . substr($votante->apellidos, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-black text-white leading-tight truncate">{{ $votante->nombres }} {{ $votante->apellidos }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <i class="fa-solid fa-id-card text-slate-600 mr-1"></i> ID {{ $votante->id }}
                        @if($votante->dpi)
                            &nbsp;&bull;&nbsp;
                            <i class="fa-solid fa-fingerprint text-slate-600 mr-1"></i> {{ $votante->dpi }}
                        @endif
                    </p>
                    <p class="text-xs text-indigo-300 font-semibold mt-1">
                        <i class="fa-solid fa-church text-xs mr-1"></i>{{ $votante->ministerio ?? 'General' }}
                    </p>
                </div>
            </div>

            {{-- Mensajes de sesión --}}
            @if(session('success'))
            <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-3 rounded-2xl text-xs text-center font-medium">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 p-3 rounded-2xl text-xs text-center font-medium">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $errors->first() }}
            </div>
            @endif

            {{-- Formulario PIN --}}
            <form action="{{ route('votar.acceder') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 text-center">PIN de la Ronda Actual</label>
                    <input type="text" name="pin" required maxlength="5" autocomplete="off"
                           class="input-pin block w-full bg-slate-900/60 border border-slate-600 rounded-2xl py-4 text-white placeholder-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                           placeholder="•••••"
                           oninput="this.value = this.value.toUpperCase()"
                           autofocus>
                </div>
                <button type="submit"
                        class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                    Ingresar a mi Cabina
                </button>
            </form>

            {{-- Separador --}}
            <div class="my-5 flex items-center gap-3">
                <div class="flex-1 h-px bg-slate-700/50"></div>
                <span class="text-slate-600 text-[10px] font-semibold uppercase tracking-wider">¿Otro votante?</span>
                <div class="flex-1 h-px bg-slate-700/50"></div>
            </div>

            {{-- Botón Cambiar Votante --}}
            <form action="{{ route('votar.cambiar-votante') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full py-3 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/25 text-amber-400 font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    Cambiar Votante / Cerrar Sesión
                </button>
            </form>
            <p class="text-center text-slate-600 text-[10px] mt-2 leading-relaxed">
                Usa esta opción si el miembro se retiró o si es el turno de otro votante
            </p>
        </div>

        {{-- ======== CASO B: Ningún votante identificado ======== --}}
        @else
        <div class="glass rounded-3xl p-8 shadow-2xl">

            @if(session('success'))
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-2xl text-sm text-center font-medium">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-2xl text-sm text-center font-medium">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $errors->first() }}
            </div>
            @endif

            @if(isset($eleccion) && $eleccion)
            <div class="mb-6 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl p-4 text-center">
                <span class="block text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-1">Elección Activa</span>
                <span class="block text-base font-extrabold text-white leading-tight mb-1">{{ $eleccion->organizacion->nombre }}</span>
                <span class="block text-xs text-indigo-200/80 font-bold uppercase tracking-wider mb-1">{{ $eleccion->puesto_en_curso }}</span>
                <span class="block text-[10px] text-slate-400 font-medium">{{ $eleccion->titulo }}</span>
            </div>
            @endif

            <p class="text-slate-400 text-sm text-center mb-6">
                Ingresa el PIN de la ronda para acceder a la cabina de votación.
            </p>

            <form action="{{ route('votar.acceder') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 text-center">PIN de la Ronda Actual</label>
                    <input type="text" name="pin" required maxlength="5" autocomplete="off"
                           class="input-pin block w-full bg-slate-900/50 border border-slate-600 rounded-2xl py-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                           placeholder="•••••"
                           oninput="this.value = this.value.toUpperCase()"
                           autofocus>
                </div>
                <button type="submit"
                        class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
                    Ingresar a la Cabina
                </button>
            </form>
        </div>
        @endif

        <p class="text-center mt-6 text-xs text-slate-600">
            &copy; {{ date('Y') }} AD Rey de Reyes &mdash; Sufragio Seguro y Confidencial
        </p>
    </div>
</body>
</html>
