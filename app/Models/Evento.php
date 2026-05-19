<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'tipo', 'fecha_inicio', 'fecha_fin', 
        'ubicacion', 'google_calendar_event_id', 'meet_link'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];
}
