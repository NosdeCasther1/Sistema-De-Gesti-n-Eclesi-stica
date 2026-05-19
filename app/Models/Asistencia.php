<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = [
        'miembro_id', 'evento_id', 'celula_id', 'fecha', 'hora', 'latitud', 'longitud'
    ];

    public function miembro()
    {
        return $this->belongsTo(Miembro::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function celula()
    {
        return $this->belongsTo(Celula::class);
    }
}
