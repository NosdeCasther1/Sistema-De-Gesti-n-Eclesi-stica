<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'initial_balance',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'account_id');
    }

    protected function balance(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $income = $this->transactions()
                    ->where('status', 'completed')
                    ->where('type', 'income')
                    ->sum('amount');

                $expense = $this->transactions()
                    ->where('status', 'completed')
                    ->where('type', 'expense')
                    ->sum('amount');

                return round((float) $attributes['initial_balance'] + $income - $expense, 2);
            }
        );
    }

    /**
     * Relación con Organización vinculada (una a una).
     */
    public function organizacion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Organizacion::class, 'financial_account_id');
    }
}
