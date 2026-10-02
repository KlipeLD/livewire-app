<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FuelType;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'mileage',
        'registration_number',
        'fuel_type',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage' => 'integer',
            'fuel_type' => FuelType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function refuelings(): HasMany
    {
        return $this->hasMany(Refueling::class);
    }
}