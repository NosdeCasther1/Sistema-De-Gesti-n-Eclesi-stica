<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidato extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleccion_id',
        'miembro_id',
        'puesto_postulado',
    ];

    /**
     * Relación con la Elección.
     */
    public function eleccion(): BelongsTo
    {
        return $this->belongsTo(Eleccion::class, 'eleccion_id');
    }

    /**
     * Relación con el Miembro (el Candidato en sí).
     */
    public function miembro(): BelongsTo
    {
        return $this->belongsTo(Miembro::class, 'miembro_id');
    }

    /**
     * Relación con los Votos recibidos.
     */
    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'candidato_id');
    }
}
