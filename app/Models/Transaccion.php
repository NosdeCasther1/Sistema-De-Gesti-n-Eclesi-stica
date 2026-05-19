<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $fillable = [
        'categoria_id', 'miembro_id', 'monto', 'fecha', 'descripcion', 'metodo_pago'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaFinanciera::class, 'categoria_id');
    }

    public function miembro()
    {
        return $this->belongsTo(Miembro::class);
    }
}
