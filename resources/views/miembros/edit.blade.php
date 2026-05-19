@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card-module p-4 max-w-4xl mx-auto" style="max-width: 800px; margin: 0 auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-white mb-0">Editar Miembro: {{ $miembro->nombres }}</h3>
            <a href="{{ route('miembros.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>

        <form action="{{ route('miembros.update', $miembro->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Sección: Datos Personales --}}
            <div class="form-section-title"><i class="fas fa-user-circle"></i> Datos Personales</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nombres *</label>
                    <input type="text" name="nombres" class="form-control" value="{{ old('nombres', $miembro->nombres) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $miembro->apellidos) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">DPI / Identidad *</label>
                    <input type="text" name="dpi" class="form-control" value="{{ old('dpi', $miembro->dpi) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $miembro->fecha_nacimiento ? $miembro->fecha_nacimiento->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccionar Sexo</option>
                        <option value="M" {{ old('sexo', $miembro->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $miembro->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado Civil</label>
                    <select name="estado_civil" class="form-select">
                        <option value="">Seleccionar Estado Civil</option>
                        <option value="Soltero(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Soltero(a)' ? 'selected' : '' }}>Soltero(a)</option>
                        <option value="Casado(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                        <option value="Divorciado(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                        <option value="Viudo(a)" {{ old('estado_civil', $miembro->estado_civil) == 'Viudo(a)' ? 'selected' : '' }}>Viudo(a)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $miembro->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $miembro->telefono) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dirección Residencial</label>
                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $miembro->direccion) }}" placeholder="Ej: 4ta Calle 5-20 Zona 1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ciudad / Municipio</label>
                    <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $miembro->ciudad) }}" placeholder="Ej: Guatemala">
                </div>
            </div>

            {{-- Sección: Información Académica y Laboral --}}
            <div class="form-section-title"><i class="fas fa-graduation-cap"></i> Información Académica y Laboral</div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Nivel Académico</label>
                    <select name="nivel_academico" class="form-select">
                        <option value="">Seleccionar Nivel</option>
                        <option value="Primaria" {{ old('nivel_academico', $miembro->nivel_academico) == 'Primaria' ? 'selected' : '' }}>Primaria</option>
                        <option value="Básicos" {{ old('nivel_academico', $miembro->nivel_academico) == 'Básicos' ? 'selected' : '' }}>Básicos</option>
                        <option value="Diversificado" {{ old('nivel_academico', $miembro->nivel_academico) == 'Diversificado' ? 'selected' : '' }}>Diversificado</option>
                        <option value="Universitario" {{ old('nivel_academico', $miembro->nivel_academico) == 'Universitario' ? 'selected' : '' }}>Universitario</option>
                        <option value="Maestría / Postgrado" {{ old('nivel_academico', $miembro->nivel_academico) == 'Maestría / Postgrado' ? 'selected' : '' }}>Maestría / Postgrado</option>
                        <option value="Ninguno" {{ old('nivel_academico', $miembro->nivel_academico) == 'Ninguno' ? 'selected' : '' }}>Ninguno</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profesión / Oficio</label>
                    <input type="text" name="profesion" class="form-control" value="{{ old('profesion', $miembro->profesion) }}" placeholder="Ej: Perito Contador, Maestra, Comerciante...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lugar de Trabajo o Estudio</label>
                    <input type="text" name="lugar_trabajo_estudio" class="form-control" value="{{ old('lugar_trabajo_estudio', $miembro->lugar_trabajo_estudio) }}" placeholder="Empresa o Institución">
                </div>
            </div>

            {{-- Sección: Información Ministerial --}}
            <div class="form-section-title"><i class="fas fa-church"></i> Información Ministerial</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Ministerio</label>
                    <input type="text" name="ministerio" class="form-control" value="{{ old('ministerio', $miembro->ministerio) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Etapa de Consolidación</label>
                    <select name="etapa_consolidacion" class="form-select">
                        <option value="Nuevo"             {{ $miembro->etapa_consolidacion == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                        <option value="En Discipulado"    {{ $miembro->etapa_consolidacion == 'En Discipulado' ? 'selected' : '' }}>En Discipulado</option>
                        <option value="Asignado a Célula" {{ $miembro->etapa_consolidacion == 'Asignado a Célula' ? 'selected' : '' }}>Asignado a Célula</option>
                        <option value="Bautizado"         {{ $miembro->etapa_consolidacion == 'Bautizado' ? 'selected' : '' }}>Bautizado</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Integración / Bautismo</label>
                    <input type="date" name="fecha_integracion" class="form-control" value="{{ old('fecha_integracion', $miembro->fecha_integracion ? $miembro->fecha_integracion->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Familia</label>
                    <select name="familia_id" class="form-select">
                        <option value="">Seleccionar Familia (Opcional)</option>
                        @foreach(\App\Models\Familia::orderBy('nombre')->get() as $f)
                            <option value="{{ $f->id }}" {{ old('familia_id', $miembro->familia_id) == $f->id ? 'selected' : '' }}>{{ $f->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Sección: Fotografía --}}
            <div class="form-section-title"><i class="fas fa-camera"></i> Fotografía de Perfil</div>
            <div class="row g-3 mb-5">
                <div class="col-md-12">
                    <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif">
                    <div class="form-text">Opcional. Máximo 2MB. Formatos: JPG, PNG, GIF. (Dejar en blanco para conservar la foto actual)</div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-top border-secondary border-opacity-25 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="{{ route('miembros.show', $miembro->id) }}" class="btn btn-outline-secondary px-4">Volver al Perfil</a>
            </div>
        </form>
    </div>
</div>
@endsection
