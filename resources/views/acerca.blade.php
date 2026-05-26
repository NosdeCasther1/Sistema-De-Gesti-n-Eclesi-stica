@extends('layouts.app')

@section('title', 'Acerca de - SGE Rey de Reyes')
@section('header_title', 'Acerca del Sistema')
@section('header_subtitle', 'Información del sistema, versión y especificaciones técnicas.')
@section('header_icon')
    <i class="fa-solid fa-circle-info text-lg"></i>
@endsection

@push('styles')
<style>
    /* Hero Banner Customization */
    .about-hero {
        background: linear-gradient(135deg, #3f39cc 0%, #6d28d9 100%) !important;
        border: 1px solid rgba(99, 102, 241, 0.25) !important;
        color: #ffffff !important;
    }
    .about-hero h1, 
    .about-hero p, 
    .about-hero span {
        color: #ffffff !important;
    }

    /* Bento Grid Cards */
    .about-card {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .about-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    [data-theme='dark'] .about-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.3) !important;
    }
    .about-card h3 {
        color: var(--text-primary) !important;
    }
    .about-card p {
        color: var(--text-secondary) !important;
    }
    .about-card-footer {
        border-top: 1px solid var(--border-color) !important;
        color: var(--text-muted) !important;
    }

    /* Inner Specification Cards */
    .about-spec-card {
        background-color: var(--bg-body) !important;
        border: 1px solid var(--border-color) !important;
        padding: 1rem !important;
        border-radius: 1rem !important;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .about-spec-card:hover {
        border-color: var(--bs-primary) !important;
    }
    .about-spec-card .spec-label {
        color: var(--text-muted) !important;
        font-weight: 700 !important;
        font-size: 0.68rem !important;
        margin-top: 0.25rem;
    }
    .about-spec-card .spec-value {
        color: var(--text-primary) !important;
        font-weight: 800 !important;
        font-size: 0.82rem !important;
        margin-top: 0.15rem;
    }

    /* Creator Card Accent */
    .creator-card {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.03) 0%, rgba(109, 40, 217, 0.03) 100%) !important;
        border: 1px solid rgba(99, 102, 241, 0.2) !important;
    }
    [data-theme='dark'] .creator-card {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(109, 40, 217, 0.08) 100%) !important;
        border-color: rgba(99, 102, 241, 0.25) !important;
    }
    .creator-divider {
        border-color: rgba(99, 102, 241, 0.15) !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-[1200px] mx-auto space-y-6">
    {{-- Hero Banner: Big Gradient Card --}}
    <div class="about-hero rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden flex flex-col md:flex-row items-center gap-6 justify-between">
        <div class="space-y-3 z-10 text-center md:text-left">
            <span class="inline-block py-1 px-3 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white text-[10px] font-black uppercase tracking-widest">
                Asamblea de Dios Rey de Reyes
            </span>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight leading-none uppercase">
                Sistema de Gestión Eclesiástica (SGE)
            </h1>
            <p class="text-indigo-100/80 text-sm max-w-xl font-medium">
                Una plataforma ministerial centralizada para la administración de membresía, finanzas, células de estudio, asistencia en tiempo real y procesos electorales confidenciales.
            </p>
        </div>
        {{-- Info Badge --}}
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center shrink-0 z-10 w-full md:w-auto">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-indigo-200">Versión del Sistema</span>
            <span class="text-2xl font-black font-mono">v1.0.0</span>
            <span class="block text-[9px] text-indigo-300 font-bold uppercase mt-1 tracking-widest">Edición Bento</span>
        </div>
        {{-- Decorative Background Circles --}}
        <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    {{-- Bento Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Card 1: Membresía --}}
        <div class="about-card rounded-3xl p-6 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center text-indigo-650 dark:text-indigo-400 mb-4">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <h3 class="text-base font-black uppercase tracking-tight mb-2">Control de Membresía</h3>
                <p class="text-xs leading-relaxed">
                    Gestión de perfiles de miembros, registro de sacramentos, generación de carnets con códigos QR, cartas de recomendación, traslados y certificados de bautismo.
                </p>
            </div>
            <div class="mt-6 pt-4 about-card-footer flex items-center justify-between text-[10px] font-bold uppercase tracking-wider">
                <span>Fichas de Miembro</span>
                <span class="text-indigo-600 dark:text-indigo-450 font-extrabold">Activo</span>
            </div>
        </div>

        {{-- Card 2: Tesorería --}}
        <div class="about-card rounded-3xl p-6 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-4">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <h3 class="text-base font-black uppercase tracking-tight mb-2">Tesorería & Caja</h3>
                <p class="text-xs leading-relaxed">
                    Registro contable de ingresos y egresos, asignación por categorías de ofrendas/diezmos, transferencias internas y reportes de cortes de caja en PDF.
                </p>
            </div>
            <div class="mt-6 pt-4 about-card-footer flex items-center justify-between text-[10px] font-bold uppercase tracking-wider">
                <span>Control de Fondos</span>
                <span class="text-emerald-600 dark:text-emerald-450 font-extrabold">Protegido</span>
            </div>
        </div>

        {{-- Card 3: Células --}}
        <div class="about-card rounded-3xl p-6 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-4">
                    <i class="fa-solid fa-network-wired text-xl"></i>
                </div>
                <h3 class="text-base font-black uppercase tracking-tight mb-2">Células y Sectores</h3>
                <p class="text-xs leading-relaxed">
                    Organización sectorial por hogares, control de asistencia semanal, asignación de líderes de célula y supervisión del crecimiento eclesiástico.
                </p>
            </div>
            <div class="mt-6 pt-4 about-card-footer flex items-center justify-between text-[10px] font-bold uppercase tracking-wider">
                <span>Sectores Territoriales</span>
                <span class="text-amber-600 dark:text-amber-450 font-extrabold">Activo</span>
            </div>
        </div>

        {{-- Card 4: Seguridad Electoral --}}
        <div class="about-card rounded-3xl p-6 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-4">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <h3 class="text-base font-black uppercase tracking-tight mb-2">Escrutinio Seguro</h3>
                <p class="text-xs leading-relaxed">
                    Motor de sufragio digital que segrega el registro de asistencia del voto emitido, garantizando el anonimato absoluto con cálculo inmutable de resultados.
                </p>
            </div>
            <div class="mt-6 pt-4 about-card-footer flex items-center justify-between text-[10px] font-bold uppercase tracking-wider">
                <span>Voto Anónimo</span>
                <span class="text-rose-650 dark:text-rose-450 font-extrabold">Certificado</span>
            </div>
        </div>

        {{-- Card 5: Asistencia QR --}}
        <div class="about-card rounded-3xl p-6 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-100 dark:border-cyan-500/20 flex items-center justify-center text-cyan-600 dark:text-cyan-400 mb-4">
                    <i class="fa-solid fa-qrcode text-xl"></i>
                </div>
                <h3 class="text-base font-black uppercase tracking-tight mb-2">Asistencia Rápida</h3>
                <p class="text-xs leading-relaxed">
                    Módulo de escaneo instantáneo para registro de asistencia en eventos y asambleas generales mediante la lectura del carnet QR oficial de los miembros.
                </p>
            </div>
            <div class="mt-6 pt-4 about-card-footer flex items-center justify-between text-[10px] font-bold uppercase tracking-wider">
                <span>Lectora QR</span>
                <span class="text-cyan-600 dark:text-cyan-450 font-extrabold">Verificado</span>
            </div>
        </div>

        {{-- Card 6: Creador & Desarrollo --}}
        <div class="about-card creator-card rounded-3xl p-6 shadow-sm">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                    <i class="fa-solid fa-laptop-code text-xl"></i>
                </div>
                <h3 class="text-base font-black uppercase tracking-tight mb-2">Creador & Desarrollo</h3>
                <p class="text-xs leading-relaxed mb-4">
                    Diseñado y desarrollado para optimizar y modernizar la gestión ministerial y electoral de la iglesia <strong>AD Rey de Reyes</strong>.
                </p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                        <div class="flex-1 min-w-0">
                            <span class="block text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-0.5">Autor Principal</span>
                            <span class="text-xs font-black text-slate-800 dark:text-white">NosdeCasther Dev</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                        <div class="flex-1 min-w-0">
                            <span class="block text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-0.5">Ingeniería de IA</span>
                            <span class="text-xs font-black text-slate-800 dark:text-white">Antigravity AI (Google DeepMind)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t creator-divider flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-455">
                <span>Asamblea 2026</span>
                <span class="px-2.5 py-0.5 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full font-black">SGE</span>
            </div>
        </div>
    </div>

    {{-- Technical Specs (Bento Footer Card) --}}
    <div x-data="{ techModalOpen: false, techTitle: '', techDesc: '', techIcon: '', techColor: '', openModal(title, desc, icon, color) { this.techTitle = title; this.techDesc = desc; this.techIcon = icon; this.techColor = color; this.techModalOpen = true; } }" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-6 shadow-sm">
        <h3 class="text-base font-black text-slate-800 dark:text-white uppercase tracking-tight mb-4">Especificaciones Técnicas</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="about-spec-card cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 transition-all"
                 @click="openModal('Laravel 11.x', 'Laravel es el framework PHP utilizado para la lógica del servidor (backend). Nos proporciona una base sólida, enrutamiento seguro, ORM avanzado para bases de datos y herramientas integradas de autenticación, ideal para crear un sistema robusto y escalable.', 'fa-brands fa-laravel', 'text-rose-500')">
                <i class="fa-brands fa-laravel text-rose-500 text-2xl mb-1"></i>
                <span class="spec-label uppercase tracking-wider">Framework</span>
                <span class="spec-value">Laravel 11.x</span>
            </div>
            <div class="about-spec-card cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 transition-all"
                 @click="openModal('Alpine.js / JS', 'Alpine.js nos brinda el comportamiento interactivo (como modales, menús y pestañas) directamente en nuestro código, manteniendo la interfaz de usuario rápida y fluida sin la complejidad de frameworks más pesados.', 'fa-brands fa-js', 'text-yellow-500')">
                <i class="fa-brands fa-js text-yellow-500 text-2xl mb-1"></i>
                <span class="spec-label uppercase tracking-wider">Interactividad</span>
                <span class="spec-value">Alpine.js / JS</span>
            </div>
            <div class="about-spec-card cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 transition-all"
                 @click="openModal('Tailwind CSS', 'Tailwind CSS es el framework de utilidades utilizado para diseñar la interfaz. Nos permite crear vistas totalmente personalizadas, modernas y adaptables a cualquier dispositivo móvil con un control detallado sobre colores y tipografía.', 'fa-brands fa-css3-alt', 'text-blue-500')">
                <i class="fa-brands fa-css3-alt text-blue-500 text-2xl mb-1"></i>
                <span class="spec-label uppercase tracking-wider">Diseño</span>
                <span class="spec-value">Tailwind CSS</span>
            </div>
            <div class="about-spec-card cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:-translate-y-1 transition-all"
                 @click="openModal('MySQL 8.0', 'MySQL actúa como nuestro sistema principal de gestión de bases de datos. Almacena de manera segura y eficiente toda la información de la feligresía, registros financieros y configuraciones, garantizando alta disponibilidad e integridad.', 'fa-solid fa-database', 'text-indigo-500')">
                <i class="fa-solid fa-database text-indigo-500 text-2xl mb-1"></i>
                <span class="spec-label uppercase tracking-wider">Base de Datos</span>
                <span class="spec-value">MySQL 8.0</span>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-750 text-center text-xs text-slate-450 dark:text-slate-500 leading-relaxed max-w-2xl mx-auto font-semibold">
            Este software ha sido optimizado para asambleas presenciales y soporte remoto. Diseñado y adaptado con rigurosos estándares de seguridad ministerial para proteger la privacidad e integridad de toda la feligresía de la iglesia <strong>AD Rey de Reyes</strong>.
        </div>

        {{-- Modal de Información Técnica --}}
        <div x-show="techModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-sm p-4 md:p-0" x-cloak style="display: none;">
            <div x-show="techModalOpen" @click.away="techModalOpen = false" class="relative w-full max-w-md max-h-full rounded-2xl bg-white dark:bg-slate-900 shadow-2xl border border-slate-200 dark:border-slate-800"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <i :class="techIcon + ' ' + techColor + ' text-2xl'"></i>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white" x-text="techTitle"></h3>
                    </div>
                    <button @click="techModalOpen = false" type="button" class="text-slate-400 bg-transparent hover:bg-slate-200 hover:text-slate-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-slate-800 dark:hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="p-4 md:p-6 space-y-4">
                    <p class="text-sm font-medium leading-relaxed text-slate-600 dark:text-slate-400" x-text="techDesc"></p>
                </div>
                <div class="flex items-center p-4 md:p-5 border-t border-slate-100 dark:border-slate-800 rounded-b bg-slate-50 dark:bg-slate-800/50">
                    <button @click="techModalOpen = false" type="button" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-xl text-sm px-5 py-3 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 shadow-md transition-all">Entendido</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
