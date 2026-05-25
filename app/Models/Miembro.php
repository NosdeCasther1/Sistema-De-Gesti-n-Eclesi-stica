<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    protected $fillable = [
        'familia_id', 'nombres', 'apellidos', 'dpi', 'fecha_nacimiento', 
        'sexo', 'estado_civil', 'telefono', 'email', 'direccion', 
        'ciudad', 'nivel_academico', 'profesion', 'lugar_trabajo_estudio',
        'es_lider', 'estado', 'foto', 'fecha_integracion', 'fecha_bautismo', 'etapa_consolidacion'
    ];

    protected $casts = [
        'fecha_integracion' => 'date',
        'fecha_bautismo' => 'date',
        'fecha_nacimiento' => 'date',
        'estado' => 'boolean',
        'es_lider' => 'boolean'
    ];

    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'miembro_ministerio');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function familia()
    {
        return $this->belongsTo(Familia::class);
    }

    public function celulas()
    {
        return $this->belongsToMany(Celula::class, 'celula_miembro');
    }

    public function celulasLideradas()
    {
        return $this->hasMany(Celula::class, 'lider_id');
    }

    public function organizaciones()
    {
        return $this->belongsToMany(Organizacion::class, 'miembro_organizacion', 'miembro_id', 'organizacion_id')
            ->withPivot('puesto', 'fecha_asignacion', 'estado')
            ->withTimestamps();
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }
}

