<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\ServiceRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_date',
        'mileage',
        'type',
        'description',
        'cost',
        'next_service_date',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'next_service_date' => 'date',
            'mileage' => 'integer',
            'cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}