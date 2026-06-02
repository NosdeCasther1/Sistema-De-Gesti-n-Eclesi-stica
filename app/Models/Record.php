<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'miembro_id',
        'fecha',
        'tipo_falta',
        'descripcion',
        'accion_tomada',
    ];

    public function miembro()
    {
        return $this->belongsTo(Miembro::class);
    }
}
