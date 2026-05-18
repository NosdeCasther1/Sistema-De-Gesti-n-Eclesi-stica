@extends('layouts.app')

@section('title', 'Nuevo Evento - AD Rey de Reyes')

@section('content')
<div class="bento-container max-w-4xl mx-auto py-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-bold mb-1 text-slate-800 dark:text-white text-2xl"><i class="fas fa-calendar-plus text-blue-500 mr-2"></i> Programar Nuevo Evento</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm m-0">Completa los datos para registrar la actividad en el sistema y sincronizarla</p>
        </div>
        <a href="{{ route('eventos.index') }}" class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2.5 rounded-full font-bold shadow-sm flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> <span>Volver a Calendario</span>
        </a>
    </div>

    <!-- Formulario Premium -->
    <form action="{{ route('eventos.store') }}" method="POST">
        @csrf
        <div class="card-module p-6 shadow-sm flex-shrink-0">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Título del Evento -->
                <div class="md:col-span-8">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2"><i class="fas fa-heading text-blue-500 mr-1"></i> Título del Evento *</label>
                    <input type="text" name="titulo" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('titulo') ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" value="{{ old('titulo') }}" placeholder="Ej. Culto de Adoración y Milagros" required autofocus>
                    @error('titulo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Evento -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2"><i class="fas fa-tag text-blue-500 mr-1"></i> Tipo de Actividad *</label>
                    <select name="tipo" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('tipo') ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                        <option value="Servicio" {{ old('tipo') === 'Servicio' ? 'selected' : '' }}>⛪ Servicio / Culto General</option>
                        <option value="Célula" {{ old('tipo') === 'Célula' ? 'selected' : '' }}>🖧 Reunión de Célula</option>
                        <option value="Reunión" {{ old('tipo') === 'Reunión' ? 'selected' : '' }}>👥 Reunión Ministerial</option>
                        <option value="Especial" {{ old('tipo') === 'Especial' ? 'selected' : '' }}>⭐ Evento Especial / Congreso</option>
                    </select>
                    @error('tipo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha y Hora de Inicio -->
                <div class="md:col-span-6">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2"><i class="far fa-calendar-check text-emerald-500 mr-1"></i> Fecha y Hora de Inicio *</label>
                    <input type="datetime-local" name="fecha_inicio" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('fecha_inicio') ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" value="{{ old('fecha_inicio', $fechaDefecto) }}" required>
                    @error('fecha_inicio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha y Hora de Fin -->
                <div class="md:col-span-6">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2"><i class="far fa-calendar-times text-red-500 mr-1"></i> Fecha y Hora de Finalización</label>
                    <input type="datetime-local" name="fecha_fin" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('fecha_fin') ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" value="{{ old('fecha_fin') }}">
                    @error('fecha_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Opcional. Si se omite, se calculará 1 hora de duración por defecto.</p>
                </div>

                <!-- Ubicación -->
                <div class="md:col-span-12">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> Ubicación / Dirección Física</label>
                    <input type="text" name="ubicacion" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('ubicacion') ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" value="{{ old('ubicacion', 'Templo Principal, AD Rey de Reyes') }}" placeholder="Ej. Salón Juvenil, Templo Principal, o Dirección de Célula">
                    @error('ubicacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-12">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2"><i class="fas fa-align-left text-blue-500 mr-1"></i> Descripción o Notas Adicionales</label>
                    <textarea name="descripcion" rows="4" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('descripcion') ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' }} bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Agrega detalles del programa, predicadores invitados, o instrucciones especiales para los asistentes...">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sincronización Google Calendar -->
                <div class="md:col-span-12 border-t border-slate-200 dark:border-slate-700/50 pt-6 mt-2">
                    <div class="p-5 rounded-xl flex items-center justify-between flex-wrap gap-4" style="background-color: var(--bg-body); border-color: var(--border-color); border-width: 1px;">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-bold text-xl flex items-center justify-center flex-shrink-0">
                                <i class="fab fa-google"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-slate-800 dark:text-white mb-1">Sincronización con Google Calendar</h6>
                                <p class="text-slate-500 dark:text-slate-400 text-xs m-0">Agrega este evento automáticamente al calendario oficial y genera un enlace de Google Meet.</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end">
                            @if($isConnected)
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="sincronizar_google" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                </label>
                            @else
                                <label class="relative inline-flex items-center cursor-not-allowed opacity-50" title="Debes conectar Google Calendar en Configuración primero">
                                    <input type="checkbox" disabled class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer dark:bg-slate-700 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600"></div>
                                </label>
                                <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 px-2.5 py-1 rounded-full text-xs font-bold mt-2">Requiere Conexión</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="md:col-span-12 border-t border-slate-200 dark:border-slate-700/50 pt-6 mt-2 flex justify-end gap-3">
                    <a href="{{ route('eventos.index') }}" class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 px-6 py-2.5 rounded-full font-bold shadow-sm transition-colors">Cancelar</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full font-bold shadow-sm flex items-center gap-2 transition-colors">
                        <i class="fas fa-calendar-check"></i> Guardar Evento
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
