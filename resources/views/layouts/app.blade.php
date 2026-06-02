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
    
    <!-- Error boundary tracker script -->
    <script>
        window.addEventListener('error', function(e) {
            console.error('Captured Error:', e);
            setTimeout(function() {
                var container = document.getElementById('js-runtime-error-box');
                var message = document.getElementById('js-runtime-error-msg');
                if (container && message) {
                    container.classList.remove('hidden');
                    container.style.display = 'block';
                    message.innerHTML += '<div class="py-1 border-b border-red-500/30">❌ ' + e.message + ' <br><span class="opacity-75">en ' + e.filename + ':' + e.lineno + '</span></div>';
                }
            }, 100);
        });
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Captured Rejection:', e);
            setTimeout(function() {
                var container = document.getElementById('js-runtime-error-box');
                var message = document.getElementById('js-runtime-error-msg');
                if (container && message) {
                    container.classList.remove('hidden');
                    container.style.display = 'block';
                    message.innerHTML += '<div class="py-1 border-b border-red-500/30">⚠️ Promise Rejection: ' + e.reason + '</div>';
                }
            }, 100);
        });
    </script>
</head>
<body>
    <!-- Error overlay box -->
    <div id="js-runtime-error-box" class="hidden" style="display:none; position: fixed; bottom: 20px; right: 20px; z-index: 100000; max-width: 450px; width: calc(100% - 40px); background: #7f1d1d; color: #fef2f2; padding: 16px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); font-family: monospace; font-size: 11px; border: 1px solid #ef4444;">
        <div style="font-weight: bold; margin-bottom: 8px; font-size: 12px; display: flex; items-center; justify-content: space-between;">
            <span>🚨 Error de JavaScript Detectado</span>
            <button onclick="document.getElementById('js-runtime-error-box').style.display='none'" style="background:transparent; border:none; color:white; cursor:pointer; font-weight:bold;">✕</button>
        </div>
        <div id="js-runtime-error-msg"></div>
    </div>
    
@php 
    try {
        $globalConfig = \App\Models\Configuracion::first() ?? new \App\Models\Configuracion(['nombre_iglesia' => 'AD REY DE REYES']);
    } catch (\Exception $e) {
        $globalConfig = (object)['nombre_iglesia' => 'AD REY DE REYES', 'logo' => null];
    }
@endphp
<div class="sidebar-backdrop lg:hidden" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
    <aside class="sidebar flex flex-col h-screen bg-white dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 pb-6" id="sidebar">
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

        <div class="flex-1 overflow-y-auto custom-scrollbar px-4 space-y-6 mb-6">
            @php
                $authUser = auth()->user();
                $currentRol = $authUser ? ($authUser->getRoleNames()->first() ?? 'administrador') : 'administrador';
                $isAdmin = $authUser && $authUser->hasRole('administrador');

                $baseClasses = "flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-bold border-l-4 border-transparent";
                $activeClasses = "bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-l-indigo-600 dark:border-l-indigo-400";
                $inactiveClasses = "text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200";
            @endphp
            <ul class="nav flex flex-col gap-1">
                <li class="nav-item">
                    @php $isActive = request()->is('/'); @endphp
                    <a href="{{ route('dashboard') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-chart-pie text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                @if($isAdmin || ($authUser && ($authUser->can('ver_miembros') || $authUser->can('ver_familias') || $authUser->can('ver_celulas'))))
                <div class="px-4 py-2 text-slate-500 font-bold tracking-wider uppercase text-xs dark:text-slate-400 mt-2 mb-1">Membresía</div>
                @if($isAdmin || ($authUser && $authUser->can('ver_miembros')))
                <li class="nav-item">
                    @php $isActive = request()->is('miembros*'); @endphp
                    <a href="{{ route('miembros.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-users text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Miembros</span>
                    </a>
                </li>
                @endif
                @if($isAdmin || ($authUser && $authUser->can('ver_familias')))
                <li class="nav-item">
                    @php $isActive = request()->is('familias*'); @endphp
                    <a href="{{ route('familias.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-home text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Familias</span>
                    </a>
                </li>
                @endif
                @if($isAdmin || ($authUser && $authUser->can('ver_celulas')))
                <li class="nav-item">
                    @php $isActive = request()->is('celulas*'); @endphp
                    <a href="{{ route('celulas.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-network-wired text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Células</span>
                    </a>
                </li>
                @endif
                @if($isAdmin)
                <li class="nav-item">
                    @php $isActive = request()->is('certificados/presentacion*'); @endphp
                    <a href="{{ route('presentacion.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-child-reaching text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Cert. Presentación</span>
                    </a>
                </li>
                <li class="nav-item">
                    @php $isActive = request()->is('certificados/matrimonio*'); @endphp
                    <a href="{{ route('matrimonio.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-rings-wedding text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Cert. Matrimonio</span>
                    </a>
                </li>
                @endif
                @if($isAdmin)
                <li class="nav-item">
                    @php $isActive = request()->is('comunicaciones/whatsapp*'); @endphp
                    <a href="{{ route('comunicaciones.whatsapp.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fa-brands fa-whatsapp text-lg {{ $isActive ? 'text-emerald-500 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>WhatsApp Web</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    @php $isActive = request()->is('organizaciones*'); @endphp
                    <a href="{{ route('organizaciones.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-sitemap text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Organizaciones/Elecciones</span>
                    </a>
                </li>
                @endif

                @if($isAdmin || ($authUser && ($authUser->can('ver_eventos') || $authUser->can('ver_asistencia') || $authUser->can('ver_tesoreria'))))
                <div class="px-4 py-2 text-slate-500 font-bold tracking-wider uppercase text-xs dark:text-slate-400 mt-3 mb-1">Ministerio</div>
                @if($isAdmin || ($authUser && $authUser->can('ver_eventos')))
                <li class="nav-item">
                    @php $isActive = request()->is('eventos*'); @endphp
                    <a href="{{ route('eventos.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-calendar-alt text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Eventos</span>
                    </a>
                </li>
                @endif
                @if($isAdmin || ($authUser && $authUser->can('ver_asistencia')))
                <li class="nav-item">
                    @php $isActive = request()->is('asistencia*'); @endphp
                    <a href="{{ route('asistencia.scanner') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-qrcode text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Asistencia QR</span>
                    </a>
                </li>
                @endif
                @if($isAdmin || ($authUser && $authUser->can('ver_tesoreria')))
                <li class="nav-item">
                    @php $isActive = request()->is('tesoreria*'); @endphp
                    <a href="{{ route('tesoreria.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-wallet text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Tesorería</span>
                    </a>
                </li>
                @endif
                @if($isAdmin || ($authUser && $authUser->can('ver_inventario')))
                <li class="nav-item">
                    @php $isActive = request()->is('inventario*'); @endphp
                    <a href="{{ route('inventario.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-boxes text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Inventario</span>
                    </a>
                </li>
                @endif
                @endif

                @if($isAdmin || ($authUser && ($authUser->can('ver_reportes') || $authUser->can('ver_configuracion'))))
                <div class="px-4 py-2 text-slate-500 font-bold tracking-wider uppercase text-xs dark:text-slate-400 mt-3 mb-1">Admin</div>
                @if($isAdmin || ($authUser && $authUser->can('ver_reportes')))
                <li class="nav-item">
                    @php $isActive = request()->is('reportes*'); @endphp
                    <a href="{{ route('reportes.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-file-invoice text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Reportes</span>
                    </a>
                </li>
                @endif
                @if($isAdmin)
                <li class="nav-item">
                    @php $isActive = request()->is('configuracion*'); @endphp
                    <a href="{{ route('configuracion.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-cog text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Configuración</span>
                    </a>
                </li>
                @endif
                @if($isAdmin)
                <li class="nav-item">
                    @php $isActive = request()->is('votar*'); @endphp
                    <a href="{{ route('votar.index') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-vote-yea text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Portal del Votante</span>
                    </a>
                </li>
                @endif
                @endif

                <li class="nav-item">
                    @php $isActive = request()->is('acerca-de*'); @endphp
                    <a href="{{ route('acerca') }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
                        <i class="fas fa-info-circle text-lg {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}"></i> <span>Acerca de</span>
                    </a>
                </li>
            </ul>
        </div>

        @if($isAdmin)
        <div class="mt-auto px-4 pb-4 shrink-0">
            <div class="flex items-center justify-center gap-2.5 text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">
                {{-- Indicador de Pulso --}}
                <div class="flex items-center gap-1.5" title="Sistema Operativo">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="uppercase">Online</span>
                </div>
                
                <span class="text-slate-300 dark:text-slate-700">•</span>
                
                {{-- Versión --}}
                <span title="Versión Actual">v1.0.0 Bento</span>
            </div>
        </div>
        @endif
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
                {{-- Simulador de Rol RBAC (Solo entorno local) --}}
                @if(app()->isLocal())
                <div class="relative" x-data="{ open: false }">
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
                @endif

                <button class="theme-toggle" onclick="toggleTheme()" title="Cambiar Tema">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>

                {{-- Menú de Usuario Logueado --}}
                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-2" role="button" tabindex="0" style="cursor: pointer;" title="Opciones de Sesión">
                        <div class="flex-col text-right hidden md:flex">
                            <span class="font-bold leading-none capitalize text-slate-800 dark:text-white" style="font-size:0.88rem;">{{ $authUser ? $authUser->nombre : $currentRol }}</span>
                            <span class="text-gray-500 leading-none mt-1 font-medium" style="font-size:0.72rem;">{{ now()->translatedFormat('l, d M') }}</span>
                        </div>
                        <div class="rounded-full flex items-center justify-center font-bold text-white shadow-sm"
                             style="width:38px;height:38px;font-size:0.9rem;background:linear-gradient(135deg,#3b82f6,#6366f1);flex-shrink:0;">
                            {{ strtoupper(substr($authUser ? $authUser->nombre : $currentRol, 0, 1)) }}
                        </div>
                    </div>
                    <ul x-show="open" @click.outside="open = false" x-transition style="display:none;" class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-2 z-50">
                        <li>
                            <div class="px-4 py-2">
                                <div class="font-bold text-slate-800 dark:text-white capitalize">{{ $authUser ? $authUser->nombre : $currentRol }}</div>
                                <div class="text-gray-500 text-xs">{{ $authUser ? $authUser->email : ($currentRol . '@iglesia.com') }}</div>
                            </div>
                        </li>
                        <li><hr class="border-t border-slate-200 dark:border-slate-700/50 my-1"></li>
                        <li>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors">
                                <i class="fa-solid fa-user-gear text-slate-400"></i> Mi Perfil / Cuenta
                            </a>
                        </li>
                        @if($isAdmin)
                        <li>
                            <a href="{{ route('configuracion.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors">
                                <i class="fa-solid fa-sliders text-slate-400"></i> Ajustes del Sistema
                            </a>
                        </li>
                        @endif
                        <li><div class="border-t border-slate-200 dark:border-slate-700/50 my-1"></div></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-rose-500 hover:bg-rose-500/10 hover:text-rose-400 transition-colors text-left font-medium">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar Sesión
                                </button>
                            </form>
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
