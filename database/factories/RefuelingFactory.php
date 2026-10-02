<?php

namespace Database\Factories;

use App\Models\Refueling;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefuelingFactory extends Factory
{
    protected $model = Refueling::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'refueled_at' => fake()->date(),
            'mileage' => fake()->numberBetween(10000, 250000),
            'liters' => fake()->randomFloat(3, 10, 80),
            'price_per_liter' => fake()->randomFloat(3, 5, 8),
            'full_tank' => fake()->boolean(80),
        ];
    }
}