<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoMatrimonio extends Model
{
    protected $fillable = [
        'esposo_id', 'esposa_id', 'fecha_matrimonio',
        'pastor_oficiante', 'testigo_1', 'testigo_2'
    ];

    protected $casts = [
        'fecha_matrimonio' => 'date',
    ];

    public function esposo()
    {
        return $this->belongsTo(Miembro::class, 'esposo_id');
    }

    public function esposa()
    {
        return $this->belongsTo(Miembro::class, 'esposa_id');
    }
}
