<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad',
        'estado',
        'ubicacion',
        'responsable_id',
        'fecha_adquisicion',
    ];

    public function responsable()
    {
        return $this->belongsTo(Miembro::class, 'responsable_id');
    }
}
