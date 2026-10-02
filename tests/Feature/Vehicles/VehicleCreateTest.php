<?php

namespace Tests\Feature\Vehicles;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class VehicleCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_vehicle(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('vehicles.create')
            ->set('form.brand', 'Toyota')
            ->set('form.model', 'Corolla')
            ->set('form.year', 2020)
            ->set('form.mileage', 75000)
            ->set('form.registrationNumber', 'kr 12 345')
            ->set('form.fuelType', 'petrol')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vehicles', [
            'user_id' => $user->id,
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2020,
            'mileage' => 75000,
            'registration_number' => 'KR12345',
            'fuel_type' => 'petrol',
        ]);
    }

    public function test_brand_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('vehicles.create')
            ->set('form.brand', '')
            ->call('save')
            ->assertHasErrors([
                'form.brand' => 'required',
            ]);

        $this->assertDatabaseCount('vehicles', 0);
    }

    public function test_year_cannot_be_in_the_future(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('vehicles.create')
            ->set('form.brand', 'Toyota')
            ->set('form.model', 'Corolla')
            ->set('form.year', now()->year + 1)
            ->set('form.mileage', 1000)
            ->set('form.registrationNumber', 'KR12345')
            ->set('form.fuelType', 'petrol')
            ->call('save')
            ->assertHasErrors([
                'form.year' => 'max',
            ]);

        $this->assertDatabaseCount('vehicles', 0);
    }

    public function test_mileage_cannot_be_negative(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('vehicles.create')
            ->set('form.brand', 'BMW')
            ->set('form.model', 'E46')
            ->set('form.year', 2003)
            ->set('form.mileage', -500)
            ->set('form.registrationNumber', 'KR98765')
            ->set('form.fuelType', 'petrol')
            ->call('save')
            ->assertHasErrors([
                'form.mileage' => 'min',
            ]);

        $this->assertDatabaseCount('vehicles', 0);
    }
}