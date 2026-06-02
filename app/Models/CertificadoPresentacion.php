<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoPresentacion extends Model
{
    protected $fillable = [
        'nino_nombre', 'nino_fecha_nacimiento', 'lugar_nacimiento',
        'padre_id', 'madre_id', 'fecha_presentacion', 'pastor_oficiante'
    ];

    protected $casts = [
        'nino_fecha_nacimiento' => 'date',
        'fecha_presentacion' => 'date',
    ];

    public function padre()
    {
        return $this->belongsTo(Miembro::class, 'padre_id');
    }

    public function madre()
    {
        return $this->belongsTo(Miembro::class, 'madre_id');
    }
}
