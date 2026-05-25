@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-[#0f172a] p-4 lg:p-8 font-sans antialiased">
    
    {{-- CONTENEDOR CENTRALIZADO (UX Focus) --}}
    <div class="max-w-3xl mx-auto space-y-6">
        
        {{-- HEADER --}}
        <div class="mb-8">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Mi Perfil / Cuenta</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Gestiona la seguridad de tu cuenta y sesiones activas.</p>
        </div>

        @if(session('status'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-2xl border border-emerald-200 dark:border-emerald-800/30 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span class="text-sm font-semibold">{{ session('status') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- TARJETA DE USUARIO (Limpia y Compacta) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm flex items-center gap-6">
            <div class="h-20 w-20 rounded-full bg-indigo-600 flex items-center justify-center text-white font-black text-3xl shadow-inner shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Administrador del Sistema' }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-1">
                    <i class="fa-regular fa-envelope"></i> {{ auth()->user()->email ?? 'admin@iglesia.com' }}
                </p>
                <div class="flex gap-2 mt-3">
                    <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider rounded border border-indigo-100 dark:border-indigo-500/20">Rol: {{ ucfirst(session('current_rol', 'administrador')) }}</span>
                    <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider rounded border border-emerald-100 dark:border-emerald-500/20">Sesión Activa</span>
                </div>
            </div>
        </div>

        {{-- TARJETA DE SEGURIDAD (Cierre Remoto) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-950/30">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <h4 class="text-base font-black text-slate-900 dark:text-white">Seguridad de la Cuenta</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Protege tu cuenta cerrando sesiones en otros dispositivos.</p>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                {{-- Alerta Informativa --}}
                <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl p-4 flex gap-3">
                    <i class="fa-solid fa-circle-info text-indigo-500 mt-0.5"></i>
                    <p class="text-xs text-indigo-900 dark:text-indigo-200 leading-relaxed">
                        Si has iniciado sesión en computadoras públicas, teléfonos de terceros u otros navegadores, puedes cerrar todas esas sesiones activas aquí por motivos de seguridad. Tu sesión actual permanecerá intacta.
                    </p>
                </div>

                {{-- Formulario --}}
                <form method="POST" action="{{ route('profile.logoutOtherDevices') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Contraseña actual para confirmar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-400"></i>
                            </div>
                            <input type="password" name="password" required placeholder="Ingresa tu contraseña actual..." class="block w-full pl-10 pr-3 py-3 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                        </div>
                        @error('password')
                            <p class="text-xs font-semibold text-rose-500 mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="pt-2">
                        {{-- Botón Premium Rose --}}
                        <button type="submit" class="px-5 py-3 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center w-full sm:w-auto cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Cerrar Sesión en Otros Dispositivos
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
