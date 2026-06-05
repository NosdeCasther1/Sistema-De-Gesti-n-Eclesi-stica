<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    protected $fillable = ['nombre', 'direccion', 'zona', 'municipio', 'departamento', 'telefono_principal', 'notas', 'celula_id'];

    public function miembros()
    {
        return $this->hasMany(Miembro::class);
    }

    public function celula()
    {
        return $this->belongsTo(Celula::class);
    }
}
