<?php

namespace Tests\Feature\Automotive;

use Livewire\Volt\Volt;
use Tests\TestCase;

class FuelCalculatorTest extends TestCase
{
    public function test_it_calculates_trip_cost(): void
    {
        Volt::test('automotive.fuel-calculator')
            ->set('distance', 500)
            ->set('consumption', 7)
            ->set('fuelPrice', 6.50)
            ->call('calculate')
            ->assertHasNoErrors()
            ->assertSet('cost', 227.50)
            ->assertSee('227,50');
    }

    public function test_distance_is_required(): void
    {
        Volt::test('automotive.fuel-calculator')
            ->set('distance', '')
            ->set('consumption', 7)
            ->set('fuelPrice', 6.50)
            ->call('calculate')
            ->assertHasErrors([
                'distance' => 'required',
            ]);
    }

    public function test_consumption_cannot_be_negative(): void
    {
        Volt::test('automotive.fuel-calculator')
            ->set('distance', 500)
            ->set('consumption', -2)
            ->set('fuelPrice', 6.50)
            ->call('calculate')
            ->assertHasErrors([
                'consumption' => 'min',
            ]);
    }

    public function test_fuel_price_must_be_numeric(): void
    {
        Volt::test('automotive.fuel-calculator')
            ->set('distance', 500)
            ->set('consumption', 7)
            ->set('fuelPrice', 'abc')
            ->call('calculate')
            ->assertHasErrors([
                'fuelPrice' => 'numeric',
            ]);
    }
}