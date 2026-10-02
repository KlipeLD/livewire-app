<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ServiceRecord>
 */
class ServiceRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'service_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'mileage' => fake()->numberBetween(1000, 300000),
            'type' => fake()->randomElement([
                'Wymiana oleju',
                'Wymiana opon',
                'Naprawa hamulców',
                'Przegląd',
            ]),
            'description' => fake()->optional()->sentence(),
            'cost' => fake()->randomFloat(2, 100, 5000),
            'next_service_date' => fake()->optional()->dateTimeBetween('now', '+2 years'),
        ];
    }
}