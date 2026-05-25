<!DOCTYPE html>
<html lang="es" class="h-full bg-white dark:bg-[#0f172a]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AD Rey de Reyes</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script>
        // Verifica localStorage o preferencias del sistema para aplicar el tema antes de renderizar
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full font-sans antialiased">
    <div class="flex min-h-full">
        
        {{-- MITAD IZQUIERDA: Formulario (Refactorizada para centrado total) --}}
        {{-- FIX: Forzado flex h-screen con justify-center e items-center para centrado absoluto --}}
        <div class="flex w-full lg:w-1/2 flex-col justify-center items-center h-screen bg-white dark:bg-[#0f172a] px-8 transition-colors duration-300 relative">
            
            {{-- BOTÓN TOGGLE TEMA (Mismo posición superior derecha de ESTA columna) --}}
            <button id="theme-toggle" type="button" class="absolute top-6 right-6 md:top-8 md:right-8 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-xl text-sm p-2.5 transition-colors shadow-sm border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 z-50">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>

            {{-- ENVOLVENTE INTERNO (w-full max-w-sm con text-center) --}}
            <div class="mx-auto w-full max-w-sm text-center -mt-16 sm:-mt-20">
                
                {{-- LOGOTIPO OFICIAL --}}
                <div class="mb-6 flex justify-center"> {{-- FIX: Justify-center para el logo --}}
                    <div class="relative inline-block rounded-2xl dark:bg-white/10 dark:p-3 dark:backdrop-blur-sm hover:scale-[1.02] transition-all duration-300">
                        <img src="{{ asset('imagen/Logo AD Rey de Reyes.png') }}?v=5" 
                             alt="Logo AD Rey de Reyes" 
                             class="w-44 sm:w-48 h-auto object-contain drop-shadow-2xl">
                    </div>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Gestión Ministerial</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Ingresa tus credenciales para acceder al sistema.</p>

                <div class="mt-8 text-left"> {{-- FIX: Text-left solo para el form por UX --}}
                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-xl p-4">
                                <p class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ $errors->first() }}</p>
                            </div>
                        @endif

                        <div>
                            <label for="email" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Correo Electrónico</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="block w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>
                        <div>
                            <label for="password" class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Contraseña</label>
                            <input id="password" name="password" type="password" required class="block w-full appearance-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 bg-white dark:bg-slate-900 dark:border-slate-700">
                            <label for="remember" class="ml-2 block text-xs font-medium text-slate-700 dark:text-slate-300">Mantener sesión activa</label>
                        </div>
                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 dark:shadow-indigo-500/10 transition-all duration-200">
                                Iniciar Sesión Oficial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MITAD DERECHA: Imagen Decorativa / Branding --}}
        <div class="relative hidden w-0 flex-1 lg:block bg-slate-900 overflow-hidden">
            {{-- Puedes reemplazar src con una foto de la iglesia o diseño --}}
            <img class="absolute inset-0 h-full w-full object-cover opacity-40 mix-blend-overlay" src="https://images.unsplash.com/photo-1438232992991-995b7058bbb3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Fondo">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
            
            <div class="absolute bottom-12 left-12 right-12 text-white">
                <h3 class="text-4xl font-black tracking-tight mb-2">Sistema AD Rey de Reyes</h3>
                <p class="text-slate-300 font-medium max-w-lg">Plataforma centralizada para la gestión ministerial, control de membresía, escrutinio electoral y tesorería.</p>
            </div>
        </div>
        
    </div>
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Cambia los iconos basado en el tema actual
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // Alternar iconos
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // Si existía preferencia previa en localStorage
            if (localStorage.getItem('theme')) {
                if (localStorage.getItem('theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            // Si NO existía preferencia
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>
