<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'brand' => fake()->randomElement([
                'Toyota',
                'BMW',
                'Lexus',
                'Audi',
                'Ford',
                'Volkswagen',
                'Nissan',
            ]),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(1990, now()->year),
            'mileage' => fake()->numberBetween(0, 350000),
            'registration_number' => fake()->unique()->bothify('?? #####'),
            'fuel_type' => fake()->randomElement([
                'petrol',
                'diesel',
                'hybrid',
                'electric',
            ]),
        ];
    }
}