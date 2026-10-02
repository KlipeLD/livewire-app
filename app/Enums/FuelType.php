<?php

namespace App\Enums;

enum FuelType: string
{
    case Petrol = 'petrol';
    case Diesel = 'diesel';
    case Hybrid = 'hybrid';
    case Electric = 'electric';

    public function label(): string
    {
        return match ($this) {
            self::Petrol => 'Benzyna',
            self::Diesel => 'Diesel',
            self::Hybrid => 'Hybryda',
            self::Electric => 'Samochód elektryczny',
        };
    }
}