<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    protected $fillable = [
        'familia_id', 'nombres', 'apellidos', 'dpi', 'fecha_nacimiento', 
        'sexo', 'estado_civil', 'telefono', 'email', 'direccion', 
        'ciudad', 'nivel_academico', 'profesion', 'lugar_trabajo_estudio',
        'ministerio', 'estado', 'foto', 'fecha_integracion', 'etapa_consolidacion'
    ];

    protected $casts = [
        'fecha_integracion' => 'date',
        'fecha_nacimiento' => 'date',
        'estado' => 'boolean'
    ];

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
}
