<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaFinanciera extends Model
{
    use SoftDeletes;

    protected $fillable = ['nombre', 'tipo'];

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'categoria_id');
    }
}
