<?php

namespace Tests\Feature\Vehicles;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class VehicleEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_vehicle(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()
            ->for($user)
            ->create([
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'mileage' => 75000,
                'registration_number' => 'KR12345',
            ]);

        $this->actingAs($user);

        Volt::test('vehicles.edit', [
            'vehicle' => $vehicle,
        ])
            ->set('form.brand', 'Toyota')
            ->set('form.model', 'Camry')
            ->set('form.year', 2021)
            ->set('form.mileage', 90000)
            ->set('form.registrationNumber', 'kr 12 345')
            ->set('form.fuelType', 'hybrid')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(
                route('vehicles.index', absolute: false)
            );

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'user_id' => $user->id,
            'brand' => 'Toyota',
            'model' => 'Camry',
            'year' => 2021,
            'mileage' => 90000,
            'registration_number' => 'KR12345',
            'fuel_type' => 'hybrid',
        ]);
    }

    public function test_user_cannot_edit_another_users_vehicle(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $vehicle = Vehicle::factory()
            ->for($anotherUser)
            ->create();

        $this->actingAs($user);

        Volt::test('vehicles.edit', [
            'vehicle' => $vehicle,
        ])->assertForbidden();
    }
}