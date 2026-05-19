<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Celula extends Model
{
    protected $fillable = [
        'nombre', 'sector', 'lider_id', 'direccion', 'dia_reunion', 'hora_reunion'
    ];

    public function lider()
    {
        return $this->belongsTo(Miembro::class, 'lider_id');
    }

    public function miembros()
    {
        return $this->belongsToMany(Miembro::class, 'celula_miembro');
    }
}
