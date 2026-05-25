<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eleccion extends Model
{
    use HasFactory;

    protected $table = 'elecciones';

    protected $fillable = [
        'organizacion_id',
        'titulo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'tipo_mayoria',
        'puesto_en_curso',
        'pin_ronda',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    /**
     * Relación con la Organización.
     */
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    /**
     * Relación con los Candidatos.
     */
    public function candidatos(): HasMany
    {
        return $this->hasMany(Candidato::class, 'eleccion_id');
    }

    /**
     * Relación con los Votos.
     */
    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'eleccion_id');
    }
}
