<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'financial_account_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Caja y Fondo Ministerial (FinancialAccount).
     */
    public function financialAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /**
     * Relación con Miembros (Muchos a Muchos).
     */
    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(Miembro::class, 'miembro_organizacion', 'organizacion_id', 'miembro_id')
            ->withPivot('puesto', 'fecha_asignacion', 'estado')
            ->withTimestamps();
    }

    /**
     * Relación con Elecciones.
     */
    public function elecciones(): HasMany
    {
        return $this->hasMany(Eleccion::class, 'organizacion_id');
    }
    /**
     * Usuarios del sistema asignados a esta organización (tenant scoping).
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'organizacion_id');
    }
}
