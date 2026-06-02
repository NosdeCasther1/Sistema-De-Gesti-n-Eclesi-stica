@extends('layouts.app')

@section('title', 'Notificaciones de WhatsApp - AD Rey de Reyes')

@section('header_title', 'Notificaciones por WhatsApp')
@section('header_subtitle', 'Genera enlaces para envíos de mensajes manuales y gratuitos')
@section('header_icon')
<i class="fa-brands fa-whatsapp fs-5 text-emerald-500"></i>
@endsection

@section('content')
<div class="container-fluid py-8 px-4 max-w-7xl mx-auto" x-data="whatsappModule()">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Sidebar / Filtros -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-filter text-emerald-500"></i> Filtros de Miembros
                </h3>
                
                <form method="GET" action="{{ route('comunicaciones.whatsapp.index') }}" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Organización</label>
                        <select name="organizacion_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <option value="">Todas las organizaciones</option>
                            @foreach($organizaciones as $org)
                                <option value="{{ $org->id }}" {{ $organizacion_id == $org->id ? 'selected' : '' }}>{{ $org->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Ministerio</label>
                        <select name="ministerio_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <option value="">Todos los ministerios</option>
                            @foreach($ministerios as $min)
                                <option value="{{ $min->id }}" {{ $ministerio_id == $min->id ? 'selected' : '' }}>{{ $min->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Etapa Espiritual</label>
                        <select name="etapa" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <option value="">Cualquier etapa</option>
                            @foreach($etapas as $e)
                                <option value="{{ $e }}" {{ $etapa == $e ? 'selected' : '' }}>{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 mt-2">
                        <i class="fa-solid fa-search"></i> Aplicar Filtros
                    </button>
                    
                    @if($organizacion_id || $ministerio_id || $etapa)
                        <a href="{{ route('comunicaciones.whatsapp.index') }}" class="block text-center mt-2 text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors">Limpiar Filtros</a>
                    @endif
                </form>
            </div>
            
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i> Envío Manual
                </h3>
                <p class="text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed mb-0">
                    Al hacer clic en "Enviar" se abrirá WhatsApp Web con el mensaje pre-escrito. Esta es una forma 100% gratuita y sin riesgos de bloqueo para notificar a los hermanos.
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Redacción del Mensaje -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-emerald-500"></i> Mensaje a Enviar
                </h3>
                
                <div class="mb-3">
                    <textarea x-model="mensaje" rows="5" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder-slate-400" placeholder="Escribe el anuncio o notificación aquí..."></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400" x-text="mensaje.length + ' caracteres'"></span>
                        <div class="flex gap-2">
                            <button @click="insertarVariable('{nombre}')" type="button" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold border border-slate-200 dark:border-slate-700 transition-colors">+ Nombre</button>
                            <button @click="insertarVariable('{apellidos}')" type="button" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold border border-slate-200 dark:border-slate-700 transition-colors">+ Apellidos</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Destinatarios -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col h-[500px]">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center shrink-0">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-0 flex items-center gap-2">
                        <i class="fa-solid fa-users text-emerald-500"></i> Destinatarios ({{ count($miembros) }})
                    </h3>
                </div>
                
                <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                    @if(count($miembros) > 0)
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[10px] uppercase font-black tracking-wider text-slate-500 dark:text-slate-400 sticky top-0 z-10 shadow-sm">
                                    <th class="px-6 py-3 border-b border-slate-200 dark:border-slate-700">Miembro</th>
                                    <th class="px-6 py-3 border-b border-slate-200 dark:border-slate-700">Teléfono</th>
                                    <th class="px-6 py-3 border-b border-slate-200 dark:border-slate-700 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @foreach($miembros as $miembro)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shrink-0">
                                                    {{ substr($miembro->nombres, 0, 1) }}{{ substr($miembro->apellidos, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900 dark:text-white mb-0">{{ $miembro->nombres }} {{ $miembro->apellidos }}</p>
                                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-0">{{ $miembro->dpi }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-medium border border-slate-200 dark:border-slate-700">
                                                <i class="fa-solid fa-phone text-[10px] opacity-50"></i>
                                                {{ $miembro->telefono }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <button @click="abrirWhatsapp('{{ $miembro->telefono }}', '{{ addslashes($miembro->nombres) }}', '{{ addslashes($miembro->apellidos) }}')"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 rounded-lg text-xs font-bold transition-all shadow-sm">
                                                <i class="fa-brands fa-whatsapp text-sm"></i> Enviar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-users-slash text-2xl text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No se encontraron miembros</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm">No hay miembros que coincidan con los filtros seleccionados o que tengan un número de teléfono registrado.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('whatsappModule', () => ({
        mensaje: '¡Hola {nombre}! Dios te bendiga. Te compartimos la siguiente información: \n\n',
        
        insertarVariable(variable) {
            this.mensaje += variable;
        },

        abrirWhatsapp(telefono, nombres, apellidos) {
            if (!telefono) {
                alert("Este miembro no tiene un número de teléfono válido.");
                return;
            }

            // Limpiar teléfono (solo números)
            let numeroLimpio = telefono.replace(/\D/g,'');
            
            // Asumimos código de país (Ejemplo: Guatemala 502) - Opcional, si no lo tiene
            if (numeroLimpio.length === 8) {
                numeroLimpio = '502' + numeroLimpio; 
            }

            // Reemplazar variables
            let msg = this.mensaje
                        .replace(/{nombre}/g, nombres)
                        .replace(/{apellidos}/g, apellidos);
            
            let url = `https://wa.me/${numeroLimpio}?text=${encodeURIComponent(msg)}`;
            
            window.open(url, '_blank');
        }
    }));
});
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(16, 185, 129, 0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: rgba(16, 185, 129, 0.4); }
</style>
@endsection
