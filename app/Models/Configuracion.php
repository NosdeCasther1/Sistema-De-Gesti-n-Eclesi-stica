<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $fillable = [
        'nombre_iglesia',
        'pastor_general',
        'direccion',
        'telefono',
        'email',
        'logo',
        'moneda'
    ];
}
