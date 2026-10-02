<?php

namespace App\Models;

use Database\Factories\RefuelingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refueling extends Model
{
    /** @use HasFactory<RefuelingFactory> */
    use HasFactory;

    protected $fillable = [
        'refueled_at',
        'mileage',
        'liters',
        'price_per_liter',
        'full_tank',
    ];

    protected function casts(): array
    {
        return [
            'refueled_at' => 'date',
            'mileage' => 'integer',
            'liters' => 'decimal:3',
            'price_per_liter' => 'decimal:3',
            'full_tank' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}