<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.backgroundColor = '#0f172a';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.backgroundColor = '#f8fafc';
            }
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AD Rey de Reyes')</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ time() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js v3 CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body>
    
@php 
    try {
        $globalConfig = \App\Models\Configuracion::first() ?? new \App\Models\Configuracion(['nombre_iglesia' => 'AD REY DE REYES']);
    } catch (\Exception $e) {
        $globalConfig = (object)['nombre_iglesia' => 'AD REY DE REYES', 'logo' => null];
    }
@endphp
<div class="sidebar-backdrop lg:hidden" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
    <aside class="sidebar border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-container flex items-center justify-between w-full mb-4">
                <div class="flex items-center gap-2">
                    @if($globalConfig && $globalConfig->logo)
                        <img src="{{ asset('storage/config/' . $globalConfig->logo) }}" style="max-height: 55px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                    @else
                        {{-- Premium Placeholder --}}
                        <div class="flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800/50 bg-slate-100 dark:bg-slate-800/30 shadow-sm dark:shadow-md" style="height: 50px; width: 50px;">
                            <i class="fas fa-church text-slate-800 dark:text-white text-xl opacity-80 dark:opacity-60"></i>
                        </div>
                    @endif
                </div>
                <button class="btn btn-link text-slate-500 dark:text-slate-400 lg:hidden p-1 ml-auto" onclick="toggleSidebar()" title="Cerrar menú">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div>
                <h5 class="font-bold mb-1 text-slate-800 dark:text-white" style="font-size: 1.15rem; letter-spacing: -0.02em;">{{ $globalConfig->nombre_iglesia ?? 'AD REY DE REYES' }}</h5>
                <p class="text-xs text-gray-500 mb-0 font-normal" style="letter-spacing: 0.02em; opacity: 0.6;">Gestión Ministerial</p>
            </div>
        </div>

        <div class="flex-grow overflow-auto py-2">
            <ul class="nav flex flex-col gap-1">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                    </a>
                </li>
                @php
                    $currentRol = session('current_rol', 'administrador');
                    $rolePermissions = session('role_permissions', [
                        'administrador' => ['miembros', 'familias', 'celulas', 'eventos', 'asistencia', 'tesoreria', 'reportes', 'configuracion'],
                        'tesorero' => ['miembros', 'familias', 'eventos', 'asistencia', 'tesoreria', 'reportes'],
                        'lider' => ['miembros', 'familias', 'celulas', 'eventos', 'asistencia'],
                        'ujier' => ['asistencia']
                    ]);
                    $activeModules = $rolePermissions[$currentRol] ?? [];
                @endphp
                
                @if(in_array('miembros', $activeModules) || in_array('familias', $activeModules) || in_array('celulas', $activeModules))
                <div class="px-4 py-2 text-slate-500 font-bold tracking-wider uppercase text-xs dark:text-slate-400 mt-2 mb-1">Membresía</div>
                @if(in_array('miembros', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('miembros.index') }}" class="nav-link {{ request()->is('miembros*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> <span>Miembros</span>
                    </a>
                </li>
                @endif
                @if(in_array('familias', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('familias.index') }}" class="nav-link {{ request()->is('familias*') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> <span>Familias</span>
                    </a>
                </li>
                @endif
                @if(in_array('celulas', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('celulas.index') }}" class="nav-link {{ request()->is('celulas*') ? 'active' : '' }}">
                        <i class="fas fa-network-wired"></i> <span>Células</span>
                    </a>
                </li>
                @endif
                @endif

                @if(in_array('eventos', $activeModules) || in_array('asistencia', $activeModules) || in_array('tesoreria', $activeModules))
                <div class="px-4 py-2 text-slate-500 font-bold tracking-wider uppercase text-xs dark:text-slate-400 mt-3 mb-1">Ministerio</div>
                @if(in_array('eventos', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('eventos.index') }}" class="nav-link {{ request()->is('eventos*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> <span>Eventos</span>
                    </a>
                </li>
                @endif
                @if(in_array('asistencia', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('asistencia.scanner') }}" class="nav-link {{ request()->is('asistencia*') ? 'active' : '' }}">
                        <i class="fas fa-qrcode"></i> <span>Asistencia QR</span>
                    </a>
                </li>
                @endif
                @if(in_array('tesoreria', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('tesoreria.index') }}" class="nav-link {{ request()->is('tesoreria*') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> <span>Tesorería</span>
                    </a>
                </li>
                @endif
                @endif

                @if(in_array('reportes', $activeModules) || in_array('configuracion', $activeModules))
                <div class="px-4 py-2 text-slate-500 font-bold tracking-wider uppercase text-xs dark:text-slate-400 mt-3 mb-1">Admin</div>
                @if(in_array('reportes', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> <span>Reportes</span>
                    </a>
                </li>
                @endif
                @if(in_array('configuracion', $activeModules))
                <li class="nav-item">
                    <a href="{{ route('configuracion.index') }}" class="nav-link {{ request()->is('configuracion*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> <span>Configuración</span>
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-700/50 mt-auto">
            @php
                $gcConnected = session()->has('google_calendar_token');
            @endphp
            <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800/50 bg-slate-50 dark:bg-slate-800/30 shadow-sm text-left">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-slate-800 dark:text-white text-xs"><i class="fas fa-server text-blue-500 mr-1"></i> Estado del Sistema</span>
                    <span class="bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400 border border-emerald-500/30 rounded-full px-2 py-1 font-bold flex items-center gap-1" style="font-size: 0.68rem;">
                        <i class="fas fa-circle" style="font-size: 0.45rem; animation: pulse 2s infinite;"></i> <span>Online</span>
                    </span>
                </div>
                <div class="flex items-center justify-between text-gray-500 mb-1" style="font-size: 0.74rem;">
                    <span>Sincronización API</span>
                    <span class="{{ $gcConnected ? 'text-emerald-500' : 'text-amber-500' }} font-semibold">
                        <i class="{{ $gcConnected ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle' }} mr-1"></i> {{ $gcConnected ? 'Conectada' : 'Inactiva' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-gray-500" style="font-size: 0.74rem;">
                    <span>Versión Actual</span>
                    <span class="font-semibold text-slate-600 dark:text-slate-400">v1.2.0 Bento</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            {{-- Left: Mobile toggle & Page Title --}}
            <div class="flex items-center gap-3">
                <button class="text-gray-500 lg:hidden p-0" onclick="toggleSidebar()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                @if(View::hasSection('header_title'))
                    <div class="flex items-center gap-3 lg:pl-1">
                        @if(View::hasSection('header_icon'))
                            <div class="rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center shadow-sm hidden sm:flex" style="width: 40px; height: 40px; flex-shrink: 0;">
                                @yield('header_icon')
                            </div>
                        @endif
                        <div>
                            <h4 class="font-bold mb-0 text-slate-800 dark:text-white text-xl" style="letter-spacing: -0.02em;">@yield('header_title')</h4>
                            @if(View::hasSection('header_subtitle'))
                                <p class="text-gray-500 text-sm mb-0 font-medium hidden md:block" style="font-size: 0.78rem;">@yield('header_subtitle')</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Theme + Simulador RBAC + User --}}
            <div class="flex items-center gap-3">
                {{-- Simulador de Rol RBAC --}}
                <div class="relative" x-data="{ open: false }">
                    @php $currentRol = session('current_rol', 'administrador'); @endphp
                    <button @click="open = !open" class="border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-full px-3 py-1.5 font-bold flex items-center gap-2 shadow-sm text-sm">
                        @if($currentRol === 'administrador')
                            <i class="fas fa-crown text-red-500"></i> <span class="hidden sm:inline">Administrador</span>
                        @elseif($currentRol === 'tesorero')
                            <i class="fas fa-coins text-emerald-500"></i> <span class="hidden sm:inline">Tesorero</span>
                        @elseif($currentRol === 'lider')
                            <i class="fas fa-user-tie text-cyan-500"></i> <span class="hidden sm:inline">Líder Célula</span>
                        @else
                            <i class="fas fa-user text-gray-500"></i> <span class="hidden sm:inline">Ujier</span>
                        @endif
                    </button>
                    <ul x-show="open" @click.outside="open = false" x-transition style="display:none;" class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-2 z-50">
                        <li><h6 class="px-4 py-2 text-xs font-bold uppercase text-gray-500">Simular Nivel de Acceso</h6></li>
                        <li><a class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 {{ $currentRol === 'administrador' ? 'bg-blue-500/10 text-blue-600 font-bold' : 'text-slate-700 dark:text-slate-300' }}" href="{{ route('switch.role', 'administrador') }}"><i class="fas fa-crown text-red-500 w-4"></i> Administrador (Total)</a></li>
                        <li><a class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 {{ $currentRol === 'tesorero' ? 'bg-blue-500/10 text-blue-600 font-bold' : 'text-slate-700 dark:text-slate-300' }}" href="{{ route('switch.role', 'tesorero') }}"><i class="fas fa-coins text-emerald-500 w-4"></i> Tesorero (Finanzas)</a></li>
                        <li><a class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 {{ $currentRol === 'lider' ? 'bg-blue-500/10 text-blue-600 font-bold' : 'text-slate-700 dark:text-slate-300' }}" href="{{ route('switch.role', 'lider') }}"><i class="fas fa-user-tie text-cyan-500 w-4"></i> Líder Célula (Sectores)</a></li>
                        <li><a class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 {{ $currentRol === 'ujier' ? 'bg-blue-500/10 text-blue-600 font-bold' : 'text-slate-700 dark:text-slate-300' }}" href="{{ route('switch.role', 'ujier') }}"><i class="fas fa-user text-gray-500 w-4"></i> Ujier (Asistencia QR)</a></li>
                    </ul>
                </div>

                <button class="theme-toggle" onclick="toggleTheme()" title="Cambiar Tema">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                {{-- Menú de Usuario Logueado --}}
                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-2" role="button" tabindex="0" style="cursor: pointer;" title="Opciones de Sesión">
                        <div class="flex-col text-right hidden md:flex">
                            <span class="font-bold leading-none capitalize text-slate-800 dark:text-white" style="font-size:0.88rem;">{{ session('current_rol', 'administrador') }}</span>
                            <span class="text-gray-500 leading-none mt-1 font-medium" style="font-size:0.72rem;">{{ now()->translatedFormat('l, d M') }}</span>
                        </div>
                        <div class="rounded-full flex items-center justify-center font-bold text-white shadow-sm"
                             style="width:38px;height:38px;font-size:0.9rem;background:linear-gradient(135deg,#3b82f6,#6366f1);flex-shrink:0;">
                            {{ strtoupper(substr(session('current_rol', 'administrador'), 0, 1)) }}
                        </div>
                    </div>
                    <ul x-show="open" @click.outside="open = false" x-transition style="display:none;" class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-2 z-50">
                        <li>
                            <div class="px-4 py-2">
                                <div class="font-bold text-slate-800 dark:text-white capitalize">{{ session('current_rol', 'administrador') }}</div>
                                <div class="text-gray-500 text-xs">{{ session('current_rol', 'administrador') }}@iglesia.com</div>
                            </div>
                        </li>
                        <li><hr class="border-t border-slate-200 dark:border-slate-700/50 my-1"></li>
                        @if($currentRol === 'administrador')
                        <li>
                            <a class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 text-slate-700 dark:text-slate-300" href="{{ route('configuracion.index', ['tab' => 'usuarios']) }}">
                                <i class="fas fa-user-cog text-blue-500 w-4"></i> Mi Perfil / Cuenta
                            </a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 text-slate-700 dark:text-slate-300" href="{{ route('configuracion.index', ['tab' => 'sistema']) }}">
                                <i class="fas fa-sliders-h text-cyan-500 w-4"></i> Ajustes del Sistema
                            </a>
                        </li>
                        <li><hr class="border-t border-slate-200 dark:border-slate-700/50 my-1"></li>
                        @endif
                        <li>
                            <a class="block px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 text-red-500" href="{{ route('dashboard') }}" onclick="alert('Sesión cerrada exitosamente (Simulación).');">
                                <i class="fas fa-sign-out-alt w-4"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="p-4 flex-grow">
            @if(session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="px-4 py-3 rounded-xl shadow-sm mb-4 flex items-center justify-between" role="alert" style="background: var(--alert-success); color: var(--alert-success-text);">
                    <div><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</div>
                    <button type="button" class="text-emerald-700 hover:text-emerald-900 focus:outline-none" @click="show = false"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-red-50 dark:bg-red-900/20 text-red-600 px-4 py-3 rounded-xl shadow-sm mb-4 flex items-center justify-between" role="alert">
                    <div><i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}</div>
                    <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" @click="show = false"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" class="bg-red-50 dark:bg-red-900/20 text-red-600 px-4 py-3 rounded-xl shadow-sm mb-4 card-module p-4 relative" role="alert">
                    <button type="button" class="absolute top-4 right-4 text-red-700 hover:text-red-900 focus:outline-none" @click="show = false"><i class="fas fa-times"></i></button>
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:
                    </div>
                    <ul class="mb-0 list-disc list-inside text-sm font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="p-4 text-center text-gray-500 text-sm border-t border-slate-200 dark:border-slate-700/50">
            &copy; {{ date('Y') }} AD Rey de Reyes - Sistema de Gestión Eclesiástica
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Lógica de Temas
        const html = document.documentElement;
        const themeIcon = document.getElementById('themeIcon');
        
        function toggleTheme() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
            
            if (newTheme === 'dark') {
                html.classList.add('dark');
                html.style.backgroundColor = '#0f172a';
            } else {
                html.classList.remove('dark');
                html.style.backgroundColor = '#f8fafc';
            }
        }

        function updateIcon(theme) {
            themeIcon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }

        // Cargar icono guardado
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (themeIcon) {
            updateIcon(savedTheme);
        }

        // Lógica Sidebar Móvil
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarBackdrop')?.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
