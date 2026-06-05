@extends('layouts.app')

@section('title', 'Configuración del Sistema - AD Rey de Reyes')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    /* Forzar desbloqueo de scroll vertical en HTML, BODY y MAIN-CONTENT */
    html, body, .main-content {
        overflow-y: auto !important;
        height: auto !important;
    }

    /* Bento Card Hover Effects */
    .bento-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bento-card:hover {
        transform: translateY(-4px);
    }

    /* Evitar que los círculos decorativos difuminados capturen clics */
    .blur-3xl {
        pointer-events: none !important;
    }

    /* Bento Buttons Premium (Bulletproof Gradients) */
    .btn-bento-primary {
        background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(37,99,235,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
        box-shadow: 0 6px 20px rgba(37,99,235,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-success {
        background: linear-gradient(135deg, #059669, #0d9488) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(5,150,105,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-success:hover {
        background: linear-gradient(135deg, #047857, #0f766e) !important;
        box-shadow: 0 6px 20px rgba(5,150,105,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-warning {
        background: linear-gradient(135deg, #d97706, #ea580c) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(217,119,6,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-warning:hover {
        background: linear-gradient(135deg, #b45309, #c2410c) !important;
        box-shadow: 0 6px 20px rgba(217,119,6,0.35) !important;
        transform: translateY(-2px);
    }

    .btn-bento-danger {
        background: linear-gradient(135deg, #e11d48, #dc2626) !important;
        color: white !important;
        box-shadow: 0 4px 14px rgba(225,29,72,0.25) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    .btn-bento-danger:hover {
        background: linear-gradient(135deg, #be123c, #b91c1c) !important;
        box-shadow: 0 6px 20px rgba(225,29,72,0.35) !important;
        transform: translateY(-2px);
    }

    /* Icon Boxes Premium (Bulletproof Gradients & Dimensions) */
    .config-icon-box {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        border-radius: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2) !important;
    }
    .config-icon-box i {
        color: white !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-block !important;
        font-size: 1.4rem !important;
    }

    .icon-box-primary { background: linear-gradient(135deg, #2563eb, #4f46e5) !important; color: white !important; }
    .icon-box-success { background: linear-gradient(135deg, #059669, #10b981) !important; color: white !important; }
    .icon-box-warning { background: linear-gradient(135deg, #d97706, #f59e0b) !important; color: white !important; }
    .icon-box-danger  { background: linear-gradient(135deg, #e11d48, #f43f5e) !important; color: white !important; }
    .icon-box-info    { background: linear-gradient(135deg, #0891b2, #06b6d4) !important; color: white !important; }
    .icon-box-purple  { background: linear-gradient(135deg, #8b5cf6, #a855f7) !important; color: white !important; }
</style>
@endpush

@section('header_title', 'Configuración del Sistema')
@section('header_subtitle', 'Personaliza la identidad, roles, catálogos y ajustes globales de la iglesia')
@section('header_icon')
<i class="fas fa-sliders-h fs-5 text-primary"></i>
@endsection

@section('content')
<div x-data="{ 
    tab: '{{ request('tab', session('active_tab', 'general')) }}',
    subTab: 'categorias',
    isSubmitting: false,
    showModalCrearCategoria: false,
    showModalCaja: false,
    showModalEditarCategoria: null,
    showModalEditarCaja: null,
    showModalCrearUsuario: false,
    showModalEditarUsuario: null,
    showModalCrearOrganizacion: false,
    showModalEditarOrganizacion: null,
    confirmModal: {
        open: false,
        title: '',
        message: '',
        actionUrl: '',
        method: 'POST',
        buttonText: 'Confirmar',
        buttonClass: 'btn-bento-danger'
    },
    showConfirm(title, message, actionUrl, method = 'POST', buttonText = 'Confirmar', buttonClass = 'btn-bento-danger') {
        this.confirmModal.title = title;
        this.confirmModal.message = message;
        this.confirmModal.actionUrl = actionUrl;
        this.confirmModal.method = method;
        this.confirmModal.buttonText = buttonText;
        this.confirmModal.buttonClass = buttonClass;
        this.confirmModal.open = true;
    }
}" class="py-6 px-4 max-w-7xl mx-auto">

    {{-- Navegación Premium de Pestañas (Pills compactos y de alta densidad con Armadura Z-Index) --}}
    <nav class="relative z-20 flex flex-wrap gap-2 p-2 bg-slate-100/80 dark:bg-slate-800/60 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-slate-700/80 mb-8 shadow-sm">
        <button type="button" 
                @click="tab = 'general'; console.log('Pestaña cambiada a: ' + tab)" 
                :class="tab === 'general' ? 'bg-white dark:bg-slate-700 shadow-sm text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/40 font-semibold'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all border-0 cursor-pointer">
            <i class="fas fa-building text-sm"></i> General
        </button>

        <button type="button" 
                @click="tab = 'usuarios'; console.log('Pestaña cambiada a: ' + tab)" 
                :class="tab === 'usuarios' ? 'bg-white dark:bg-slate-700 shadow-sm text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/40 font-semibold'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all border-0 cursor-pointer">
            <i class="fas fa-users text-sm"></i> Usuarios & Roles
        </button>

        <button type="button" 
                @click="tab = 'catalogos'; console.log('Pestaña cambiada a: ' + tab)" 
                :class="tab === 'catalogos' ? 'bg-white dark:bg-slate-700 shadow-sm text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/40 font-semibold'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all border-0 cursor-pointer">
            <i class="fas fa-folder-open text-sm"></i> Catálogos
        </button>

        <button type="button" 
                @click="tab = 'sistema'; console.log('Pestaña cambiada a: ' + tab)" 
                :class="tab === 'sistema' ? 'bg-white dark:bg-slate-700 shadow-sm text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/40 font-semibold'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all border-0 cursor-pointer">
            <i class="fas fa-cog text-sm"></i> Sistema
        </button>

        <button type="button" 
                @click="tab = 'integraciones'; console.log('Pestaña cambiada a: ' + tab)" 
                :class="tab === 'integraciones' ? 'bg-white dark:bg-slate-700 shadow-sm text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white/50 dark:hover:bg-slate-700/40 font-semibold'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs transition-all border-0 cursor-pointer">
            <i class="fas fa-plug text-sm"></i> Integraciones
        </button>
    </nav>


    {{-- ===== PESTAÑA 1: GENERAL ===== --}}
    <div x-show="tab === 'general'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            {{-- Formulario Principal (8 Columnas) --}}
            <div class="lg:col-span-8 flex flex-col">
                <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data" class="m-0 flex-grow flex flex-col">
                    @csrf
                    <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                        <!-- Glow de fondo -->
                        <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

                        <div>
                            {{-- Header Bento --}}
                            <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                                        <i class="fas fa-church"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Ajustes Generales de la Iglesia</h5>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Configuración de identidad ministerial, logo oficial y datos de contacto</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Seccion 1: Identidad -->
                                <div>
                                    <h6 class="font-extrabold text-blue-600 dark:text-blue-400 text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <i class="fas fa-id-card"></i>
                                        <span>1. Identidad Ministerial</span>
                                    </h6>

                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                                        <!-- Inputs Izquierda (8 Columnas) -->
                                        <div class="md:col-span-8 space-y-4">
                                            <div>
                                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre de la Iglesia *</label>
                                                <input type="text" name="nombre_iglesia" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $config->nombre_iglesia }}" required>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Pastor General</label>
                                                <input type="text" name="pastor_general" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $config->pastor_general }}">
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                                <!-- Firma Pastor (PNG Transparente) -->
                                                <div>
                                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Firma Pastor (PNG Transparente)</label>
                                                    <div class="relative group/firma flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 hover:border-blue-500/50 transition-all duration-300 min-h-[140px] text-center shadow-sm">
                                                        {{-- Grid background pattern for transparent PNG --}}
                                                        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] rounded-2xl bg-[radial-gradient(#000_1px,transparent_1px)] dark:bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px] pointer-events-none"></div>
                                                        
                                                        <input type="file" name="firma_pastor" id="firmaInput" class="hidden" accept="image/png" onchange="previewFirma(this)">
                                                        
                                                        <div id="firmaPreviewContainer" class="relative z-10 flex flex-col items-center justify-center w-full">
                                                            @if($config->firma_pastor)
                                                                <img id="firmaPreview" src="{{ asset('storage/config/' . $config->firma_pastor) }}" class="max-h-16 w-auto object-contain drop-shadow-sm mb-2 rounded-lg bg-slate-200/30 dark:bg-slate-950/30 p-2 border border-slate-300/30">
                                                                <div id="firmaPlaceholder" class="hidden"></div>
                                                            @else
                                                                <img id="firmaPreview" src="" class="max-h-16 w-auto object-contain drop-shadow-sm mb-2 rounded-lg bg-slate-200/30 dark:bg-slate-950/30 p-2 border border-slate-300/30 hidden">
                                                                <div id="firmaPlaceholder" class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 p-2">
                                                                    <i class="fas fa-signature text-2xl mb-1.5 opacity-60"></i>
                                                                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Sin firma cargada</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <label for="firmaInput" class="relative z-10 mt-2 py-2 px-4 rounded-xl border border-blue-500/30 dark:border-blue-500/40 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-bold text-[10px] flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-sm">
                                                            <i class="fas fa-camera text-sm"></i>
                                                            <span id="firmaButtonText">{{ $config->firma_pastor ? 'Cambiar Firma' : 'Subir Firma' }}</span>
                                                        </label>
                                                        
                                                        <div id="firmaFileName" class="relative z-10 text-[9px] text-slate-400 dark:text-slate-500 mt-2 max-w-full truncate font-medium">
                                                            @if($config->firma_pastor)
                                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center gap-1"><i class="fas fa-check-circle"></i> Firma activa</span>
                                                            @else
                                                                Sugerido: PNG transparente
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sello Iglesia (PNG Transparente) -->
                                                <div>
                                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Sello Iglesia (PNG Transparente)</label>
                                                    <div class="relative group/sello flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-blue-500/50 transition-all duration-300 min-h-[140px] text-center shadow-sm">
                                                        {{-- Grid background pattern for transparent PNG --}}
                                                        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] rounded-2xl bg-[radial-gradient(#000_1px,transparent_1px)] dark:bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px] pointer-events-none"></div>
                                                        
                                                        <input type="file" name="sello_iglesia" id="selloInput" class="hidden" accept="image/png" onchange="previewSello(this)">
                                                        
                                                        <div id="selloPreviewContainer" class="relative z-10 flex flex-col items-center justify-center w-full">
                                                            @if($config->sello_iglesia)
                                                                <img id="selloPreview" src="{{ asset('storage/config/' . $config->sello_iglesia) }}" class="max-h-16 w-auto object-contain drop-shadow-sm mb-2 rounded-lg bg-slate-200/30 dark:bg-slate-950/30 p-2 border border-slate-300/30">
                                                                <div id="selloPlaceholder" class="hidden"></div>
                                                            @else
                                                                <img id="selloPreview" src="" class="max-h-16 w-auto object-contain drop-shadow-sm mb-2 rounded-lg bg-slate-200/30 dark:bg-slate-950/30 p-2 border border-slate-300/30 hidden">
                                                                <div id="selloPlaceholder" class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 p-2">
                                                                    <i class="fas fa-stamp text-2xl mb-1.5 opacity-60"></i>
                                                                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Sin sello cargado</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <label for="selloInput" class="relative z-10 mt-2 py-2 px-4 rounded-xl border border-blue-500/30 dark:border-blue-500/40 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-bold text-[10px] flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-sm">
                                                            <i class="fas fa-camera text-sm"></i>
                                                            <span id="selloButtonText">{{ $config->sello_iglesia ? 'Cambiar Sello' : 'Subir Sello' }}</span>
                                                        </label>
                                                        
                                                        <div id="selloFileName" class="relative z-10 text-[9px] text-slate-400 dark:text-slate-500 mt-2 max-w-full truncate font-medium">
                                                            @if($config->sello_iglesia)
                                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center gap-1"><i class="fas fa-check-circle"></i> Sello activo</span>
                                                            @else
                                                                Sugerido: PNG transparente
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Vista Previa y Subida de Logo (4 Columnas) -->
                                        <div class="md:col-span-4 flex flex-col items-center">
                                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 text-center">Logo Oficial</label>
                                            
                                            <div class="w-full p-3 mb-3 rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/40 shadow-sm flex flex-col items-center justify-center min-h-[140px] relative group/logo">
                                                @if($config->logo)
                                                    <img id="logoPreview" src="{{ asset('storage/config/' . $config->logo) }}" class="max-h-28 w-auto object-contain drop-shadow-md rounded-lg mb-1">
                                                    <div id="logoPlaceholder" class="hidden"></div>
                                                @else
                                                    <img id="logoPreview" src="" class="max-h-28 w-auto object-contain drop-shadow-md rounded-lg mb-1 hidden">
                                                    <div id="logoPlaceholder" class="p-4 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-center">
                                                        <i class="fas fa-cloud-upload-alt text-3xl mb-2 opacity-60"></i>
                                                        <span class="text-[11px] font-medium">Sin logo asignado</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <input type="file" name="logo" id="logoInput" class="hidden" accept="image/*" onchange="previewLogo(this)">
                                            <label for="logoInput" class="w-full py-2.5 px-4 rounded-xl border border-blue-500/30 dark:border-blue-500/40 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-bold text-xs flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm mb-1">
                                                <i class="fas fa-camera text-sm"></i>
                                                <span>Cambiar Logo</span>
                                            </label>
                                            <div id="fileNameDisplay" class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 text-center font-medium">Sugerido: PNG 512x512px</div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-slate-100 dark:border-slate-800/80 my-6">

                                <!-- Seccion 2: Contacto -->
                                <div>
                                    <h6 class="font-extrabold text-blue-600 dark:text-blue-400 text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <i class="fas fa-address-book"></i>
                                        <span>2. Información de Contacto</span>
                                    </h6>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Dirección Física</label>
                                            <input type="text" name="direccion" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $config->direccion }}">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Teléfono de Oficina</label>
                                            <input type="text" name="telefono" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 8)" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $config->telefono }}">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Correo Electrónico Oficial</label>
                                            <input type="email" name="email" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $config->email }}">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Símbolo de Moneda</label>
                                            <input type="text" name="moneda" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm font-mono" value="{{ $config->moneda }}" maxlength="5">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boton Guardar -->
                        <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <button type="submit" class="btn-bento-primary px-6 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2.5 transition-all cursor-pointer">
                                <i class="fas fa-save text-sm"></i>
                                <span>Guardar Ajustes Generales</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Guía de Configuración (4 Columnas) --}}
            <div class="lg:col-span-4 flex flex-col">
                <div class="bento-card bg-gradient-to-b from-cyan-500/10 via-cyan-500/5 to-transparent dark:from-cyan-500/20 dark:via-cyan-500/10 dark:to-transparent border border-cyan-500/20 dark:border-cyan-500/30 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden flex-grow group">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-cyan-500/10 dark:bg-cyan-500/5 rounded-full blur-3xl group-hover:bg-cyan-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-cyan-500/20 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-info group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-cyan-600 dark:text-cyan-400 tracking-tight mb-1">Guía de Configuración</h5>
                                    <p class="text-xs text-cyan-600/80 dark:text-cyan-400/80 mb-0">Impacto en el sistema</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 dark:text-slate-300 mb-6 font-medium leading-relaxed">
                            Los datos ingresados en esta sección son fundamentales y aparecerán de forma automatizada en diversos módulos de la plataforma:
                        </p>
                        
                        <div class="space-y-4 mb-6">
                            <!-- Item 1 -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-cyan-500/15 dark:border-cyan-500/25 shadow-sm flex items-start gap-4">
                                <div class="rounded-xl bg-cyan-500/10 p-2.5 text-cyan-600 dark:text-cyan-400 flex items-center justify-center w-10 h-10 flex-shrink-0 mt-0.5">
                                    <i class="fas fa-file-pdf text-base"></i>
                                </div>
                                <div>
                                    <h6 class="font-bold text-slate-900 dark:text-white mb-1 text-xs">Membretes de Reportes PDF</h6>
                                    <p class="text-slate-500 dark:text-slate-400 text-[11px] mb-0 leading-relaxed">Todos los reportes financieros, de asistencia y listados incluirán el nombre y logo oficial de la iglesia.</p>
                                </div>
                            </div>

                            <!-- Item 2 -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-cyan-500/15 dark:border-cyan-500/25 shadow-sm flex items-start gap-4">
                                <div class="rounded-xl bg-cyan-500/10 p-2.5 text-cyan-600 dark:text-cyan-400 flex items-center justify-center w-10 h-10 flex-shrink-0 mt-0.5">
                                    <i class="fas fa-id-badge text-base"></i>
                                </div>
                                <div>
                                    <h6 class="font-bold text-slate-900 dark:text-white mb-1 text-xs">Carnets Digitales de Miembros</h6>
                                    <p class="text-slate-500 dark:text-slate-400 text-[11px] mb-0 leading-relaxed">Las credenciales generadas para los feligreses llevarán la firma del Pastor General y el logo institucional.</p>
                                </div>
                            </div>

                            <!-- Item 3 -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-cyan-500/15 dark:border-cyan-500/25 shadow-sm flex items-start gap-4">
                                <div class="rounded-xl bg-cyan-500/10 p-2.5 text-cyan-600 dark:text-cyan-400 flex items-center justify-center w-10 h-10 flex-shrink-0 mt-0.5">
                                    <i class="fas fa-receipt text-base"></i>
                                </div>
                                <div>
                                    <h6 class="font-bold text-slate-900 dark:text-white mb-1 text-xs">Recibos de Tesorería</h6>
                                    <p class="text-slate-500 dark:text-slate-400 text-[11px] mb-0 leading-relaxed">Los comprobantes de diezmos y ofrendas utilizarán el símbolo de moneda y los datos de contacto configurados.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-cyan-500/20 text-center text-cyan-600 dark:text-cyan-400 text-[11px] font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-shield-alt"></i>
                        <span>Configuración global protegida por nivel de acceso</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== PESTAÑA 2: USUARIOS & ROLES (CRUD Completo) ===== --}}
    <div x-show="tab === 'usuarios'" x-cloak>
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl mb-8 relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

            {{-- Header Bento --}}
            <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Gestión de Usuarios</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Administra las credenciales y niveles de acceso al sistema</p>
                    </div>
                </div>
                <button type="button" @click="showModalCrearUsuario = true" class="btn-bento-primary px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 cursor-pointer transition-all">
                    <i class="fas fa-user-plus text-sm"></i> <span>Nuevo Usuario</span>
                </button>
            </div>

            {{-- Tabla Premium Bento UI --}}
            <div class="border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm mb-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800/80">
                            <tr>
                                <th class="pl-6 pr-4 py-4">Usuario</th>
                                <th class="py-4 text-center">Rol / Nivel</th>
                                <th class="py-4 text-center">Fecha Creación</th>
                                <th class="text-right pr-6 pl-4 py-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50">
                            @foreach($usuarios as $user)
                                <tr class="transition-all hover:bg-slate-50/80 dark:hover:bg-slate-800/30 text-xs">
                                    <td class="pl-6 pr-4 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 font-black flex items-center justify-center shadow-sm w-10 h-10 flex-shrink-0 text-sm border border-blue-500/20">
                                                {{ strtoupper(substr($user->nombre, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white mb-0.5 text-xs">{{ $user->nombre }}</div>
                                                <div class="text-slate-500 dark:text-slate-400 text-[11px]">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        @php $userRole = $user->getRoleNames()->first() ?? 'ujier'; @endphp
                                        @if($userRole === 'administrador')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20 uppercase tracking-wider">
                                                <i class="fas fa-crown text-xs"></i> Administrador
                                            </span>
                                        @elseif($userRole === 'tesorero')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 uppercase tracking-wider">
                                                <i class="fas fa-coins text-xs"></i> Tesorero
                                            </span>
                                        @elseif($userRole === 'lider')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 uppercase tracking-wider">
                                                <i class="fas fa-user-tie text-xs"></i> Líder Célula
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 uppercase tracking-wider">
                                                <i class="fas fa-user text-xs"></i> Ujier
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center text-slate-500 dark:text-slate-400 text-[11px] font-medium">
                                        <i class="fas fa-calendar-alt mr-1.5 opacity-60"></i> {{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="py-4 text-right pr-6 pl-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" @click="showModalEditarUsuario = {{ $user->id }}" class="w-9 h-9 rounded-full border border-blue-500/20 dark:border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Editar Usuario">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            @if($usuarios->count() > 1)
                                                <button type="button" @click="showConfirm(
                                                    'Eliminar Usuario del Sistema',
                                                    '¿Estás seguro de eliminar el usuario «{{ addslashes($user->nombre) }}»? Perderá el acceso inmediato a la plataforma.',
                                                    '{{ route('usuarios.destroy', $user->id) }}',
                                                    'DELETE',
                                                    'Sí, Eliminar Usuario',
                                                    'btn-bento-danger'
                                                )" class="w-9 h-9 rounded-full border border-rose-200 dark:border-rose-800/80 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Eliminar Usuario">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            @else
                                                <button type="button" class="w-9 h-9 rounded-full border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed opacity-50 shadow-sm" disabled title="No se puede eliminar el único usuario">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SECCIÓN MATRIZ DE PERMISOS POR ROL (RBAC DINÁMICO) --}}
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl mb-8 relative overflow-hidden group">
            <!-- Glow de fondo -->
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all duration-500"></div>

            {{-- Header Bento --}}
            <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="config-icon-box icon-box-warning group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Matriz de Permisos por Rol</h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Configura dinámicamente a qué módulos tiene acceso cada nivel de usuario en el sistema</p>
                    </div>
                </div>
            </div>

            @php
                // Cargar permisos desde Spatie en lugar de sesión
                $permissionToModule = [
                    'ver_miembros' => 'miembros',
                    'ver_familias' => 'familias',
                    'ver_celulas' => 'celulas',
                    'ver_asistencia' => 'asistencia',
                    'ver_tesoreria' => 'tesoreria',
                    'ver_reportes' => 'reportes',
                    'ver_configuracion' => 'configuracion',
                ];
                $rolePermissions = [];
                foreach (['administrador', 'tesorero', 'lider', 'ujier'] as $rn) {
                    $spatieRole = \Spatie\Permission\Models\Role::findByName($rn, 'web');
                    $rolePermissions[$rn] = $spatieRole->permissions->pluck('name')->map(fn($p) => $permissionToModule[$p] ?? null)->filter()->values()->toArray();
                }
                $modulosDisponibles = [
                    'miembros' => ['nombre' => '👥 Miembros', 'desc' => 'Gestión de feligreses'],
                    'familias' => ['nombre' => '🏠 Familias', 'desc' => 'Núcleos familiares'],
                    'celulas' => ['nombre' => '🖧 Células', 'desc' => 'Grupos de crecimiento'],
                    'asistencia' => ['nombre' => '📱 Asistencia QR', 'desc' => 'Control de eventos'],
                    'tesoreria' => ['nombre' => '🪙 Tesorería', 'desc' => 'Ingresos y egresos'],
                    'reportes' => ['nombre' => '📊 Reportes', 'desc' => 'Informes y exportaciones'],
                    'configuracion' => ['nombre' => '⚙️ Configuración', 'desc' => 'Ajustes del sistema']
                ];
                $rolesDisponibles = [
                    'administrador' => ['nombre' => '👑 Administrador', 'bloqueado' => true],
                    'tesorero' => ['nombre' => '🪙 Tesorero', 'bloqueado' => false],
                    'lider' => ['nombre' => '👥 Líder Célula', 'bloqueado' => false],
                    'ujier' => ['nombre' => '📱 Ujier', 'bloqueado' => false]
                ];
            @endphp

            <form action="{{ route('permisos.update') }}" method="POST" class="m-0">
                @csrf
                <div class="border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800/80">
                                <tr>
                                    <th class="pl-6 pr-4 py-4 min-w-[220px]">Módulo / Sección</th>
                                    @foreach($rolesDisponibles as $rolKey => $rolData)
                                        <th class="py-4 text-center min-w-[140px]">{{ $rolData['nombre'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50 text-xs">
                                @foreach($modulosDisponibles as $modKey => $modData)
                                    <tr class="transition-all hover:bg-slate-50/80 dark:hover:bg-slate-800/30">
                                        <td class="pl-6 pr-4 py-4">
                                            <div class="font-bold text-slate-900 dark:text-white mb-0.5 text-xs">{{ $modData['nombre'] }}</div>
                                            <div class="text-slate-500 dark:text-slate-400 text-[11px]">{{ $modData['desc'] }}</div>
                                        </td>
                                        @foreach($rolesDisponibles as $rolKey => $rolData)
                                            @php
                                                $hasPermission = in_array($modKey, $rolePermissions[$rolKey] ?? []);
                                            @endphp
                                            <td class="py-4 text-center">
                                                @if($rolData['bloqueado'])
                                                    <div class="inline-flex items-center justify-center">
                                                        <input type="checkbox" class="w-5 h-5 rounded-lg border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-blue-600 focus:ring-blue-500/20 cursor-not-allowed opacity-60 shadow-sm" checked disabled>
                                                        <input type="hidden" name="permisos[{{ $rolKey }}][]" value="{{ $modKey }}">
                                                    </div>
                                                @else
                                                    <div class="inline-flex items-center justify-center">
                                                        <input type="checkbox" name="permisos[{{ $rolKey }}][]" value="{{ $modKey }}" class="w-5 h-5 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-blue-600 focus:ring-blue-500/20 cursor-pointer shadow-sm transition-all" {{ $hasPermission ? 'checked' : '' }}>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-4 pt-5 border-t border-slate-100 dark:border-slate-800">
                    <div class="text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-base"></i>
                        <span>El rol de Administrador tiene acceso permanente e inmodificable a todas las áreas del sistema.</span>
                    </div>
                    <button type="submit" class="btn-bento-primary px-6 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2.5 transition-all cursor-pointer">
                        <i class="fas fa-user-shield text-sm"></i> <span>Guardar Matriz de Permisos</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- MODAL CREAR USUARIO (TAILWIND PURO + ALPINE) --}}
        <template x-if="showModalCrearUsuario">
            <div class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Overlay -->
                <div @click="document.getElementById('formModalCrearUsuario')?.reset(); showModalCrearUsuario = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
                
                <!-- Contenedor del Modal -->
                <div 
                     @keydown.escape.window="document.getElementById('formModalCrearUsuario')?.reset(); showModalCrearUsuario = false"
                     class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800/80 max-w-md w-full overflow-hidden z-10 text-left p-0">
                    
                    <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3 text-base">
                            <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span>Nuevo Usuario</span>
                        </h6>
                        <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 border-0 bg-transparent cursor-pointer transition-colors" @click="document.getElementById('formModalCrearUsuario')?.reset(); showModalCrearUsuario = false">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form id="formModalCrearUsuario" action="{{ route('usuarios.store') }}" method="POST" @submit="if ($el.checkValidity()) { isSubmitting = true }" class="m-0">
                        @csrf
                        <div class="p-6 text-left space-y-4">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre Completo *</label>
                                <input type="text" name="nombre" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="Ej. Juan Pérez" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Correo Electrónico *</label>
                                <input type="email" name="email" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="ejemplo@iglesia.com" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Rol / Nivel de Acceso *</label>
                                <select name="rol" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer" required>
                                    <option value="administrador">👑 Administrador (Acceso Total)</option>
                                    <option value="tesorero">🪙 Tesorero (Solo Finanzas y Miembros)</option>
                                    <option value="lider" selected>👥 Líder de Célula (Solo Células y Familias)</option>
                                    <option value="ujier">📱 Ujier (Solo Asistencia QR)</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Organización / Departamento</label>
                                <select name="organizacion_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                                    <option value="">Ninguna (Acceso Global / Sin asignar)</option>
                                    @foreach($organizaciones as $org)
                                        <option value="{{ $org->id }}" {{ (isset($user) && $user->organizacion_id == $org->id) ? 'selected' : '' }}>
                                            {{ $org->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-500 mt-1">Obligatorio para Tesoreros y Líderes para restringir su acceso.</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Contraseña Inicial *</label>
                                <div class="flex rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                                    <input type="text" name="password" id="createPassword" class="w-full bg-transparent px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none border-0" placeholder="Mínimo 6 caracteres" required>
                                    <button type="button" class="px-4 border-l border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors border-0 cursor-pointer flex items-center justify-center" onclick="generatePassword('createPassword')" title="Generar clave segura">
                                        <i class="fas fa-key text-xs"></i>
                                    </button>
                                </div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium">El usuario podrá usar esta clave para ingresar al sistema.</div>
                            </div>
                        </div>
                        <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                            <button type="button" @click="document.getElementById('formModalCrearUsuario')?.reset(); showModalCrearUsuario = false" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">Cancelar</button>
                            <button type="submit" :disabled="isSubmitting" class="btn-bento-primary px-6 py-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting" class="flex items-center gap-2">
                                    <i class="fas fa-user-check"></i> <span>Crear Usuario</span>
                                </span>
                                <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Procesando...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- MODAL EDITAR USUARIO (TAILWIND PURO + ALPINE) --}}
        @foreach($usuarios as $user)
            <template x-if="showModalEditarUsuario === {{ $user->id }}">
                <div class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <!-- Overlay -->
                    <div @click="document.getElementById('formModalEditarUsuario_{{ $user->id }}')?.reset(); showModalEditarUsuario = null" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
                    
                    <!-- Contenedor del Modal -->
                    <div 
                         @keydown.escape.window="document.getElementById('formModalEditarUsuario_{{ $user->id }}')?.reset(); showModalEditarUsuario = null"
                         class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800/80 max-w-md w-full overflow-hidden z-10 text-left p-0">
                        
                        <div class="border-b border-slate-200 dark:border-slate-800 p-6 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                            <h6 class="font-bold text-slate-900 dark:text-white mb-0 flex items-center gap-3 text-base">
                                <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <span>Editar Usuario</span>
                            </h6>
                            <button type="button" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 border-0 bg-transparent cursor-pointer transition-colors" @click="document.getElementById('formModalEditarUsuario_{{ $user->id }}')?.reset(); showModalEditarUsuario = null">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <form id="formModalEditarUsuario_{{ $user->id }}" action="{{ route('usuarios.update', $user->id) }}" method="POST" @submit="if ($el.checkValidity()) { isSubmitting = true }" class="m-0">
                            @csrf
                            @method('PUT')
                            <div class="p-6 text-left space-y-4">
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nombre Completo *</label>
                                    <input type="text" name="nombre" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $user->nombre }}" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Correo Electrónico *</label>
                                    <input type="email" name="email" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ $user->email }}" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Rol / Nivel de Acceso *</label>
                                    <select name="rol" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer" required>
                                        @php $editUserRole = $user->getRoleNames()->first() ?? 'ujier'; @endphp
                                        <option value="administrador" {{ $editUserRole === 'administrador' ? 'selected' : '' }}>👑 Administrador (Acceso Total)</option>
                                        <option value="tesorero" {{ $editUserRole === 'tesorero' ? 'selected' : '' }}>🪙 Tesorero (Solo Finanzas y Miembros)</option>
                                        <option value="lider" {{ $editUserRole === 'lider' ? 'selected' : '' }}>👥 Líder de Célula (Solo Células y Familias)</option>
                                        <option value="ujier" {{ $editUserRole === 'ujier' ? 'selected' : '' }}>📱 Ujier (Solo Asistencia QR)</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Organización / Departamento</label>
                                    <select name="organizacion_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer">
                                        <option value="">Ninguna (Acceso Global / Sin asignar)</option>
                                        @foreach($organizaciones as $org)
                                            <option value="{{ $org->id }}" {{ (isset($user) && $user->organizacion_id == $org->id) ? 'selected' : '' }}>
                                                {{ $org->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1">Obligatorio para Tesoreros y Líderes para restringir su acceso.</p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Nueva Contraseña (Opcional)</label>
                                    <div class="flex rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                                        <input type="text" name="password" id="editPassword_{{ $user->id }}" class="w-full bg-transparent px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none border-0" placeholder="Dejar en blanco para mantener actual">
                                        <button type="button" class="px-4 border-l border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors border-0 cursor-pointer flex items-center justify-center" onclick="generatePassword('editPassword_{{ $user->id }}')" title="Generar clave segura">
                                            <i class="fas fa-key text-xs"></i>
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium">Mínimo 6 caracteres si deseas cambiarla.</div>
                                </div>
                            </div>
                            <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/50 flex justify-end gap-3">
                                <button type="button" @click="document.getElementById('formModalEditarUsuario_{{ $user->id }}')?.reset(); showModalEditarUsuario = null" class="px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl transition-all border-0 bg-transparent cursor-pointer">Cancelar</button>
                                <button type="submit" :disabled="isSubmitting" class="btn-bento-primary px-6 py-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 disabled:opacity-50 transition-all cursor-pointer disabled:cursor-not-allowed">
                                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                                        <i class="fas fa-save"></i> <span>Guardar Cambios</span>
                                    </span>
                                    <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Procesando...</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        @endforeach
    </div>

          {{-- ===== PESTAÑA 3: CATÁLOGOS ===== --}}
    <div x-show="tab === 'catalogos'" x-cloak>
        {{-- LAYOUT DE SUB-PESTAÑAS PARA CATÁLOGOS --}}
        <div x-data="{ subTab: 'categorias' }" class="w-full">
            
            {{-- Menú de Píldoras (Pills) --}}
            <div class="flex gap-2 mb-6 overflow-x-auto border-b border-slate-200 dark:border-slate-800 pb-4 custom-scrollbar">
                <button @click="subTab = 'categorias'" :class="subTab === 'categorias' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 border-transparent'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all border whitespace-nowrap">
                    <i class="fa-solid fa-tags mr-2"></i> Categorías
                </button>
                <button @click="subTab = 'cajas'" :class="subTab === 'cajas' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 border-transparent'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all border whitespace-nowrap">
                    <i class="fa-solid fa-vault mr-2"></i> Cajas y Fondos
                </button>
                <button @click="subTab = 'organizaciones'" :class="subTab === 'organizaciones' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 border-transparent'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all border whitespace-nowrap">
                    <i class="fa-solid fa-sitemap mr-2"></i> Organizaciones
                </button>
                <button @click="subTab = 'ministerios'" :class="subTab === 'ministerios' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 border-transparent'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all border whitespace-nowrap">
                    <i class="fa-solid fa-users-rays mr-2"></i> Ministerios
                </button>
                <button @click="subTab = 'etapas'" :class="subTab === 'etapas' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/50 border-transparent'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all border whitespace-nowrap">
                    <i class="fa-solid fa-seedling mr-2"></i> Etapas
                </button>
            </div>

            {{-- Contenedor: CATEGORIAS --}}
            <div x-show="subTab === 'categorias'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="max-w-5xl">
                {{-- 1. Categorías Financieras --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-warning group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Categorías Financieras</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Gestión de rubros de ingresos y egresos para Tesorería</p>
                                </div>
                            </div>
                            <button type="button" @click="showModalCrearCategoria = true" class="btn-bento-warning px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 cursor-pointer transition-all">
                                <i class="fas fa-plus text-sm"></i> <span>Nueva Categoría</span>
                            </button>
                        </div>

                        <div class="border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm mb-4">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800/80">
                                        <tr>
                                            <th class="pl-6 pr-4 py-4">Nombre de Categoría</th>
                                            <th class="py-4 text-center">Tipo</th>
                                            <th class="text-right pr-6 pl-4 py-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50 text-xs">
                                        @foreach($categorias as $cat)
                                            <tr class="transition-all hover:bg-slate-50/80 dark:hover:bg-slate-800/30 {{ $cat->trashed() ? 'opacity-60 bg-slate-50/50 dark:bg-slate-800/30' : '' }}">
                                                <td class="pl-6 pr-4 py-4">
                                                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                                                        <span>{{ $cat->nombre }}</span>
                                                        @if($cat->trashed())
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 uppercase tracking-wider">Archivado</span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 uppercase tracking-wider">Activo</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="py-4 text-center">
                                                    @if($cat->tipo === 'ingreso')
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-wider">
                                                            <i class="fas fa-arrow-down text-xs"></i> Ingreso
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 uppercase tracking-wider">
                                                            <i class="fas fa-arrow-up text-xs"></i> Gasto / Egreso
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-4 text-right pr-6 pl-4">
                                                    <div class="flex items-center justify-end gap-2">
                                                        @if(!$cat->trashed())
                                                            <button type="button" @click="showModalEditarCategoria = {{ $cat->id }}" class="w-9 h-9 rounded-full border border-blue-500/20 dark:border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Editar Categoría">
                                                                <i class="fas fa-edit text-xs"></i>
                                                            </button>
                                                            <button type="button" @click="showConfirm(
                                                                'Archivar Categoría Financiera',
                                                                '¿Estás seguro de archivar la categoría financiera «{{ addslashes($cat->nombre) }}»? Los datos históricos y reportes previos se mantendrán intactos.',
                                                                '{{ route('categorias.destroy', $cat->id) }}',
                                                                'DELETE',
                                                                'Sí, Archivar Categoría',
                                                                'btn-bento-danger'
                                                            )" class="w-9 h-9 rounded-full border border-rose-200 dark:border-rose-800/80 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Archivar Categoría">
                                                                <i class="fas fa-trash-alt text-xs"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" @click="showConfirm(
                                                                'Restaurar Categoría Financiera',
                                                                '¿Estás seguro de restaurar y reactivar la categoría «{{ addslashes($cat->nombre) }}»? Volverá a estar disponible en el módulo de tesorería.',
                                                                '{{ route('categorias.restore', $cat->id) }}',
                                                                'POST',
                                                                'Sí, Restaurar Categoría',
                                                                'btn-bento-success'
                                                            )" class="w-9 h-9 rounded-full border border-emerald-200 dark:border-emerald-800/80 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Restaurar Categoría">
                                                                <i class="fas fa-rotate-left text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-auto">
                        <i class="fas fa-info-circle text-amber-500 text-base"></i>
                        <span>Las categorías financieras estructuran los reportes contables de la iglesia.</span>
                    </div>
                </div>
            </div>

            {{-- Contenedor: CAJAS --}}
            <div x-show="subTab === 'cajas'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="max-w-5xl">
                {{-- 2. Cajas y Fondos Ministeriales --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-success group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Cajas y Fondos Ministeriales</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Fondos independientes para la administración interna.</p>
                                </div>
                            </div>
                            <button type="button" @click="showModalCaja = true" class="btn-bento-success px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 cursor-pointer transition-all">
                                <i class="fas fa-plus text-sm"></i> <span>Nueva Caja</span>
                            </button>
                        </div>

                        <div class="border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm mb-4">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800/80">
                                        <tr>
                                            <th class="pl-6 pr-4 py-4">Nombre de la Cuenta</th>
                                            <th class="py-4 text-right">Saldo Inicial</th>
                                            <th class="py-4 text-center">Estado</th>
                                            <th class="py-4 text-right pr-6 pl-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50 text-xs">
                                        @foreach($accounts as $account)
                                            <tr class="transition-all hover:bg-slate-50/80 dark:hover:bg-slate-800/30 {{ $account->trashed() ? 'opacity-60 bg-slate-50/50 dark:bg-slate-800/30' : '' }}">
                                                <td class="pl-6 pr-4 py-4 font-bold text-slate-900 dark:text-white">{{ $account->name }}</td>
                                                <td class="py-4 text-right font-mono text-slate-600 dark:text-slate-300 font-bold">Q{{ number_format($account->initial_balance, 2) }}</td>
                                                <td class="py-4 text-center">
                                                    @if($account->trashed())
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 uppercase tracking-wider">Archivado</span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-wider">Activa</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 text-right pr-6 pl-4">
                                                    <div class="flex items-center justify-end gap-2">
                                                        @if(!$account->trashed())
                                                            <button type="button" @click="showModalEditarCaja = {{ $account->id }}" class="w-9 h-9 rounded-full border border-blue-500/20 dark:border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Editar Caja">
                                                                <i class="fas fa-edit text-xs"></i>
                                                            </button>
                                                            <button type="button" @click="showConfirm(
                                                                'Archivar Caja o Fondo Ministerial',
                                                                '¿Estás seguro de archivar la caja «{{ addslashes($account->name) }}»? Los balances históricos y transacciones se mantendrán intactos para futuras auditorías.',
                                                                '{{ route('configuracion.accounts.destroy', $account->id) }}',
                                                                'DELETE',
                                                                'Sí, Archivar Caja',
                                                                'btn-bento-danger'
                                                            )" class="w-9 h-9 rounded-full border border-rose-200 dark:border-rose-800/80 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Archivar Caja">
                                                                <i class="fas fa-trash-alt text-xs"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" @click="showConfirm(
                                                                'Restaurar Caja o Fondo Ministerial',
                                                                '¿Estás seguro de restaurar y reactivar la caja «{{ addslashes($account->name) }}»? Volverá a estar activa para registrar nuevos ingresos y gastos.',
                                                                '{{ route('configuracion.accounts.restore', $account->id) }}',
                                                                'POST',
                                                                'Sí, Restaurar Caja',
                                                                'btn-bento-success'
                                                            )" class="w-9 h-9 rounded-full border border-emerald-200 dark:border-emerald-800/80 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Restaurar Caja">
                                                                <i class="fas fa-rotate-left text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-auto">
                        <i class="fas fa-info-circle text-emerald-500 text-base"></i>
                        <span>Cuentas para la gestión de fondos ministeriales específicos.</span>
                    </div>
                </div>
            </div>

            {{-- Contenedor: ORGANIZACIONES --}}
            <div x-show="subTab === 'organizaciones'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="max-w-5xl">
                {{-- 3. Organizaciones y Comités --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-purple group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-sitemap"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Organizaciones y Comités</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Gestión de sociedades, comités y directivas ministeriales.</p>
                                </div>
                            </div>
                            <button type="button" @click="showModalCrearOrganizacion = true" class="btn-bento-primary px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 cursor-pointer transition-all">
                                <i class="fas fa-plus text-sm"></i> <span>Nueva Organización</span>
                            </button>
                        </div>

                        <div class="border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-sm mb-4">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800/80">
                                        <tr>
                                            <th class="pl-6 pr-4 py-4">Organización</th>
                                            <th class="py-4 text-center">Fondo Vinculado</th>
                                            <th class="py-4 text-center">Estado</th>
                                            <th class="py-4 text-right pr-6 pl-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/50 text-xs">
                                        @foreach($organizaciones as $org)
                                            <tr class="transition-all hover:bg-slate-50/80 dark:hover:bg-slate-800/30 {{ !$org->estado ? 'opacity-60 bg-slate-50/50 dark:bg-slate-800/30' : '' }}">
                                                <td class="pl-6 pr-4 py-4">
                                                    <div class="font-bold text-slate-900 dark:text-white">{{ $org->nombre }}</div>
                                                    <div class="text-slate-500 dark:text-slate-400 text-[10px] font-normal mt-0.5">{{ Str::limit($org->descripcion, 60) }}</div>
                                                </td>
                                                <td class="py-4 text-center">
                                                    @if($org->financialAccount)
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                                                            <i class="fas fa-vault text-[10px]"></i> {{ $org->financialAccount->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 dark:text-slate-600 italic">Ninguno</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 text-center">
                                                    @if(!$org->estado)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20 uppercase tracking-wider">Archivado</span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-wider">Activa</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 text-right pr-6 pl-4">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button type="button" @click="showModalEditarOrganizacion = {{ $org->id }}" class="w-9 h-9 rounded-full border border-blue-500/20 dark:border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Editar Organización">
                                                            <i class="fas fa-edit text-xs"></i>
                                                        </button>
                                                        @if($org->estado)
                                                            <button type="button" @click="showConfirm(
                                                                'Archivar Organización',
                                                                '¿Estás seguro de archivar la organización «{{ addslashes($org->nombre) }}»? Ya no estará visible para votaciones ni asignación pública.',
                                                                '{{ route('configuracion.organizaciones.destroy', $org->id) }}',
                                                                'DELETE',
                                                                'Sí, Archivar',
                                                                'btn-bento-danger'
                                                            )" class="w-9 h-9 rounded-full border border-rose-200 dark:border-rose-800/80 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Archivar Organización">
                                                                <i class="fas fa-trash-alt text-xs"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" @click="showConfirm(
                                                                'Restaurar Organización',
                                                                '¿Estás seguro de reactivar la organización «{{ addslashes($org->nombre) }}»?',
                                                                '{{ route('configuracion.organizaciones.restore', $org->id) }}',
                                                                'POST',
                                                                'Sí, Restaurar',
                                                                'btn-bento-success'
                                                            )" class="w-9 h-9 rounded-full border border-emerald-200 dark:border-emerald-800/80 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center transition-all cursor-pointer shadow-sm" title="Restaurar Organización">
                                                                <i class="fas fa-rotate-left text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-auto">
                        <i class="fas fa-info-circle text-indigo-500 text-base"></i>
                        <span>Las organizaciones estructuran los padrones de miembros y procesos de elecciones.</span>
                    </div>
                </div>
            </div>

            {{-- Contenedor: MINISTERIOS --}}
            <div x-show="subTab === 'ministerios'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="max-w-5xl">
                {{-- 1. Ministerios Activos --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-church"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Ministerios Activos</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Listado disponible en registro de miembros</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2.5 mb-6">
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm"><i class="fas fa-music"></i> <span>Alabanza</span></span>
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm"><i class="fas fa-child"></i> <span>E. Dominical</span></span>
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm"><i class="fas fa-user-friends"></i> <span>Jóvenes</span></span>
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm"><i class="fas fa-female"></i> <span>Damas</span></span>
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm"><i class="fas fa-male"></i> <span>Caballeros</span></span>
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm"><i class="fas fa-hand-holding-heart"></i> <span>Ujieres</span></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-auto">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-base"></i>
                        <span>Estos valores se asignan dinámicamente al crear o editar feligreses.</span>
                    </div>
                </div>
            </div>

            {{-- Contenedor: ETAPAS --}}
            <div x-show="subTab === 'etapas'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="max-w-5xl">
                {{-- 2. Etapas de Consolidación --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-cyan-500/10 dark:bg-cyan-500/5 rounded-full blur-3xl group-hover:bg-cyan-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-info group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-shoe-prints"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Etapas de Consolidación</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Ruta de crecimiento espiritual del miembro</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3.5 mb-6">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 shadow-sm">
                                <span class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-3"><i class="fas fa-seedling text-emerald-500 text-lg"></i> <span>1. Nuevo Creyente</span></span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 uppercase tracking-wider">Inicial</span>
                            </div>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 shadow-sm">
                                <span class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-3"><i class="fas fa-book-open text-blue-600 dark:text-blue-400 text-lg"></i> <span>2. En Discipulado</span></span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 uppercase tracking-wider">Formación</span>
                            </div>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 shadow-sm">
                                <span class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-3"><i class="fas fa-users text-cyan-600 dark:text-cyan-400 text-lg"></i> <span>3. Asignado a Célula</span></span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 uppercase tracking-wider">Comunidad</span>
                            </div>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 shadow-sm">
                                <span class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-3"><i class="fas fa-water text-indigo-500 text-lg"></i> <span>4. Bautizado</span></span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 uppercase tracking-wider">Pleno</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-auto">
                        <i class="fas fa-info-circle text-cyan-600 dark:text-cyan-400 text-base"></i>
                        <span>Ruta de seguimiento para la consolidación de nuevos miembros.</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== PESTAÑA 4: SISTEMA ===== --}}
    <div x-show="tab === 'sistema'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            {{-- Columna Izquierda: Configuración SMTP y Servidor --}}
            <div class="lg:col-span-7 flex flex-col">
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-rose-500/10 dark:bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-danger group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Ajustes del Sistema y Servidor</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Configuración regional y credenciales de envío de correo SMTP</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('sistema.update') }}" method="POST" class="m-0 space-y-6">
                            @csrf
                            <div class="space-y-6">
                                {{-- Zona Horaria --}}
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-1.5"><i class="fas fa-globe-americas text-blue-600 dark:text-blue-400"></i> <span>Zona Horaria del Sistema *</span></label>
                                    <select name="timezone" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer" required>
                                        @php $currentTz = session('timezone', 'America/Guatemala'); @endphp
                                        <option value="America/Guatemala" {{ $currentTz === 'America/Guatemala' ? 'selected' : '' }}>🇬🇹 America/Guatemala (UTC-06:00)</option>
                                        <option value="America/Mexico_City" {{ $currentTz === 'America/Mexico_City' ? 'selected' : '' }}>🇲🇽 America/Mexico_City (UTC-06:00)</option>
                                        <option value="America/El_Salvador" {{ $currentTz === 'America/El_Salvador' ? 'selected' : '' }}>🇸🇻 America/El_Salvador (UTC-06:00)</option>
                                        <option value="America/Honduras" {{ $currentTz === 'America/Honduras' ? 'selected' : '' }}>🇭🇳 America/Honduras (UTC-06:00)</option>
                                        <option value="America/Costa_Rica" {{ $currentTz === 'America/Costa_Rica' ? 'selected' : '' }}>🇨🇷 America/Costa_Rica (UTC-06:00)</option>
                                        <option value="America/Bogota" {{ $currentTz === 'America/Bogota' ? 'selected' : '' }}>🇨🇴 America/Bogota (UTC-05:00)</option>
                                    </select>
                                </div>

                                {{-- SMTP Host & Port --}}
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                    <div class="md:col-span-8">
                                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-1.5"><i class="fas fa-server text-blue-600 dark:text-blue-400"></i> <span>Servidor SMTP (Mail Host) *</span></label>
                                        <input type="text" name="mail_host" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ session('mail_host', 'smtp.mailtrap.io') }}" placeholder="ej. smtp.gmail.com" required>
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Puerto *</label>
                                        <input type="number" name="mail_port" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm font-mono" value="{{ session('mail_port', 2525) }}" placeholder="ej. 587" required>
                                    </div>
                                </div>

                                {{-- SMTP User & Pass --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-1.5"><i class="fas fa-user-lock text-blue-600 dark:text-blue-400"></i> <span>Usuario SMTP</span></label>
                                        <input type="text" name="mail_username" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" value="{{ session('mail_username', '7a8b9c0d1e2f3g') }}" placeholder="Usuario de correo">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-1.5"><i class="fas fa-key text-blue-600 dark:text-blue-400"></i> <span>Contraseña SMTP</span></label>
                                        <input type="password" name="mail_password" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm" placeholder="••••••••••••">
                                    </div>
                                </div>

                                {{-- Modo Mantenimiento --}}
                                <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center justify-between p-6 rounded-3xl bg-amber-500/10 border border-amber-500/20 shadow-sm flex-wrap gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="config-icon-box icon-box-warning group-hover:scale-110 transition-transform duration-500">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div>
                                                <h6 class="font-bold text-slate-900 dark:text-white mb-1 text-sm">Modo de Mantenimiento</h6>
                                                <p class="text-xs text-slate-600 dark:text-slate-300 mb-0 leading-relaxed">Desactiva temporalmente el acceso a miembros y ujieres</p>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="relative inline-flex items-center cursor-pointer mb-0">
                                                <input type="checkbox" name="maintenance_mode" class="sr-only peer" {{ session('maintenance_mode') ? 'checked' : '' }}>
                                                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/20 dark:peer-focus:ring-amber-500/10 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-slate-600 peer-checked:bg-amber-500 shadow-sm"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                <button type="submit" class="btn-bento-danger px-6 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2.5 transition-all cursor-pointer">
                                    <i class="fas fa-save text-sm"></i> <span>Guardar Ajustes de Sistema</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Respaldo y Mantenimiento de Base de Datos --}}
            <div class="lg:col-span-5 flex flex-col gap-8">
                {{-- Tarjeta: Respaldo de Base de Datos --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-success group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Respaldo de Base de Datos</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Genera una copia de seguridad en formato SQL</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 shadow-sm text-center mb-6">
                            <i class="fas fa-cloud-download-alt text-4xl text-emerald-500 mb-4 block drop-shadow-md"></i>
                            <h6 class="font-bold text-slate-900 dark:text-white mb-2 text-sm">Copia de Seguridad Automatizada</h6>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mb-6 leading-relaxed">Descarga un archivo .sql con la estructura completa y registros actuales de la iglesia.</p>
                            
                            <form action="{{ route('sistema.backup') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn-bento-success w-full py-3.5 px-6 rounded-xl text-xs font-bold flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                                    <i class="fas fa-download text-sm"></i> <span>Generar Respaldo (SQL)</span>
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs px-2 pb-3">
                            <span>Último respaldo:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ now()->subDays(2)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs px-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <span>Tamaño estimado:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">14.8 MB</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-6">
                        <i class="fas fa-info-circle text-emerald-500 text-base"></i>
                        <span>Almacenamiento seguro de transacciones y feligreses.</span>
                    </div>
                </div>

                {{-- Tarjeta: Información del Servidor --}}
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-purple-500/10 dark:bg-purple-500/5 rounded-full blur-3xl group-hover:bg-purple-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-purple group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Estado del Entorno</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Parámetros y versiones del servidor</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3.5 mb-6">
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 text-xs">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Versión de Laravel:</span>
                                <span class="font-bold text-slate-900 dark:text-white">v11.x</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 text-xs">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Versión de PHP:</span>
                                <span class="font-bold text-slate-900 dark:text-white font-mono">{{ PHP_VERSION }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 text-xs">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Controlador DB:</span>
                                <span class="font-bold text-slate-900 dark:text-white uppercase">{{ config('database.default') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 text-xs">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Entorno (APP_ENV):</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-500/20 uppercase tracking-wider">{{ config('app.env', 'local') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-6">
                        <i class="fas fa-check-circle text-purple-500 text-base"></i>
                        <span>El sistema opera bajo los requerimientos óptimos de rendimiento.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PESTAÑA 5: INTEGRACIONES ===== --}}
    <div x-show="tab === 'integraciones'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <div class="lg:col-span-8 flex flex-col">
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden group flex-grow">
                    <!-- Glow de fondo -->
                    <div class="absolute -right-20 -top-20 w-60 h-60 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>

                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-primary group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight mb-1">Integración con Google Calendar</h5>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">Sincronización automática de eventos de la iglesia y generación de enlaces de Google Meet</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 rounded-2xl bg-slate-50/50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 mb-6 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <i class="fab fa-google text-3xl text-rose-500 drop-shadow-sm"></i>
                                    <div>
                                        <h6 class="font-bold text-slate-900 dark:text-white mb-1.5 text-xs uppercase tracking-wider">Estado de Conexión:</h6>
                                        @if(session()->has('google_calendar_token'))
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-wider"><i class="fas fa-check-circle text-xs"></i> Conectado y Activo</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 uppercase tracking-wider"><i class="fas fa-exclamation-circle text-xs"></i> No Conectado</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    @if(session()->has('google_calendar_token'))
                                        <form action="{{ route('google.calendar.disconnect') }}" method="POST" class="m-0" onsubmit="return confirm('¿Estás seguro de desvincular Google Calendar?');">
                                            @csrf
                                            <button type="submit" class="btn-bento-danger px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 cursor-pointer transition-all">
                                                <i class="fas fa-unlink text-sm"></i> <span>Desvincular Cuenta</span>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('google.calendar.connect') }}" class="btn-bento-primary px-6 py-3.5 rounded-xl text-xs font-bold flex items-center gap-2.5 no-underline transition-all">
                                            <i class="fas fa-link text-sm"></i> <span>Conectar con Google Calendar</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(session()->has('google_calendar_token'))
                            @php
                                $googleService = app(\App\Services\GoogleCalendarService::class);
                                $calendars = $googleService->listCalendars();
                                $selectedCalendarId = $googleService->getSelectedCalendarId();
                            @endphp

                            @if(session()->has('google_calendar_error'))
                                <div class="p-6 rounded-3xl mb-6 shadow-sm border border-rose-500/30 bg-rose-500/10">
                                    <h6 class="font-bold text-rose-600 dark:text-rose-400 mb-3 text-sm flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Permiso Denegado en Google Cloud Console</h6>
                                    <p class="text-xs text-rose-600 dark:text-rose-400 mb-4 leading-relaxed font-medium">
                                        Tu cuenta fue vinculada correctamente, pero el proyecto de Google Cloud no tiene habilitada la API de Google Calendar.
                                    </p>
                                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-rose-500/20 mb-4 text-xs font-mono text-slate-600 dark:text-slate-400 shadow-inner" style="word-break: break-all;">
                                        {{ session('google_calendar_error') }}
                                    </div>
                                    <p class="text-xs mb-0 font-medium text-rose-600 dark:text-rose-400 leading-relaxed">
                                        👉 <strong>Solución:</strong> Visita la consola de Google Cloud a través del enlace mostrado arriba para habilitar la <code>Google Calendar API</code> en tu proyecto. Luego, espera unos minutos y recarga esta página.
                                    </p>
                                </div>
                            @else
                                <div class="p-6 rounded-3xl bg-blue-500/10 border border-blue-500/20 mb-6 shadow-sm">
                                    <h6 class="font-bold text-slate-900 dark:text-white mb-2 text-sm flex items-center gap-2"><i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400"></i> Seleccionar Calendario de Trabajo</h6>
                                    <p class="text-slate-500 dark:text-slate-400 text-xs mb-4 leading-relaxed font-medium">Elige cuál de tus calendarios de Google deseas sincronizar con el sistema de la iglesia:</p>
                                    
                                    <form action="{{ route('google.calendar.select') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center m-0">
                                        @csrf
                                        <div class="md:col-span-9">
                                            <select name="calendar_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer" onchange="this.form.submit()">
                                                @foreach($calendars as $cal)
                                                    <option value="{{ $cal->getId() }}" {{ $selectedCalendarId === $cal->getId() ? 'selected' : '' }}>
                                                        📅 {{ $cal->getSummary() }} {{ $cal->getPrimary() ? '(Calendario Principal)' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <button type="submit" class="btn-bento-primary w-full py-3.5 px-6 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all cursor-pointer">
                                                <i class="fas fa-sync-alt text-sm"></i> <span>Sincronizar</span>
                                            </button>
                                        </div>
                                    </form>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-3 font-medium flex items-center gap-1.5">
                                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i> Al cambiar de calendario, el sistema importará automáticamente las actividades de ese calendario específico.
                                    </div>
                                </div>
                            @endif
                        @endif

                        <h6 class="font-bold text-slate-900 dark:text-white mb-3 text-xs uppercase tracking-wider flex items-center gap-2"><i class="fas fa-shield-alt text-blue-600 dark:text-blue-400"></i> Alcance de la Integración</h6>
                        <ul class="text-slate-500 dark:text-slate-400 text-xs space-y-2.5 mb-0 pl-4 font-medium leading-relaxed">
                            <li>Creación y actualización automática de eventos en el calendario principal de la cuenta vinculada.</li>
                            <li>Generación de salas virtuales de Google Meet adjuntas a los eventos híbridos o en línea.</li>
                            <li>Gestión centralizada desde el módulo de Eventos de la iglesia sin necesidad de abrir Google Calendar manualmente.</li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-xs font-medium flex items-center gap-2.5 mt-8">
                        <i class="fas fa-check-circle text-blue-600 dark:text-blue-400 text-base"></i>
                        <span>Sincronización bidireccional activa y segura.</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 flex flex-col">
                <div class="bento-card bg-gradient-to-b from-cyan-500/10 via-cyan-500/5 to-transparent dark:from-cyan-500/20 dark:via-cyan-500/10 dark:to-transparent border border-cyan-500/20 dark:border-cyan-500/30 rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden flex-grow group">
                    <div>
                        {{-- Header Bento --}}
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-cyan-500/20 flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="config-icon-box icon-box-info group-hover:scale-110 transition-transform duration-500">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-cyan-600 dark:text-cyan-400 tracking-tight mb-1">Requisitos Previos</h5>
                                    <p class="text-xs text-cyan-600/80 dark:text-cyan-400/80 mb-0">Credenciales de API de Google</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 dark:text-slate-300 mb-4 font-medium leading-relaxed">
                            Para que la integración funcione correctamente, asegúrate de que en el archivo <code>.env</code> estén configuradas las credenciales de Google Cloud Console:
                        </p>
                        <div class="bg-slate-900 text-slate-300 p-4 rounded-2xl text-xs font-mono mb-4 shadow-inner border border-slate-800 leading-relaxed" style="word-break: break-all;">
                            GOOGLE_CLIENT_ID=...<br>
                            GOOGLE_CLIENT_SECRET=...<br>
                            GOOGLE_REDIRECT_URI=...
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 leading-relaxed font-medium">
                            La URI de redirección debe coincidir exactamente con: <br>
                            <code class="text-cyan-600 dark:text-cyan-400 font-bold block mt-1">{{ route('google.calendar.callback') }}</code>
                        </p>
                    </div>

                    <div class="pt-4 border-t border-cyan-500/20 text-center text-cyan-600 dark:text-cyan-400 text-[11px] font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i> Conexión encriptada mediante OAuth 2.0
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{--    MODALES MODULARIZADOS (TAILWIND PURO)   --}}
    {{-- ========================================== --}}
    @include('configuracion.partials.modal-crear-categoria')
    @include('configuracion.partials.modal-editar-categoria')
    @include('configuracion.partials.modal-caja')
    @include('configuracion.partials.modal-editar-caja')
    @include('configuracion.partials.modal-confirmacion')
    @include('configuracion.partials.modal-crear-organizacion')
    @include('configuracion.partials.modal-editar-organizacion')
</div>
@endsection

@push('scripts')
<script>
    // Desbloqueo forzoso de scroll y limpieza de clases modales residuales
    function forceScrollUnlock() {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';
        const main = document.querySelector('.main-content');
        if (main) main.style.overflowY = 'auto';
    }
    document.addEventListener('DOMContentLoaded', forceScrollUnlock);
    document.addEventListener('alpine:init', forceScrollUnlock);
    window.addEventListener('load', forceScrollUnlock);
</script>
<script>
    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        const placeholder = document.getElementById('logoPlaceholder');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
            fileNameDisplay.innerHTML = `<span class="text-blue-600 dark:text-blue-400 font-bold"><i class="fas fa-file-image mr-1"></i> ${input.files[0].name}</span>`;
        }
    }

    function previewFirma(input) {
        const preview = document.getElementById('firmaPreview');
        const placeholder = document.getElementById('firmaPlaceholder');
        const fileNameDisplay = document.getElementById('firmaFileName');
        const buttonText = document.getElementById('firmaButtonText');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
            fileNameDisplay.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 font-bold"><i class="fas fa-check-circle mr-1"></i> Listo para guardar</span>`;
            if (buttonText) buttonText.innerText = 'Cambiar Firma';
        }
    }

    function previewSello(input) {
        const preview = document.getElementById('selloPreview');
        const placeholder = document.getElementById('selloPlaceholder');
        const fileNameDisplay = document.getElementById('selloFileName');
        const buttonText = document.getElementById('selloButtonText');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
            fileNameDisplay.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 font-bold"><i class="fas fa-check-circle mr-1"></i> Listo para guardar</span>`;
            if (buttonText) buttonText.innerText = 'Cambiar Sello';
        }
    }

    function generatePassword(inputId) {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        let password = '';
        for (let i = 0; i < 10; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById(inputId).value = password;
    }
</script>
@endpush
