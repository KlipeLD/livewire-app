<?php

namespace Tests\Feature\Vehicles;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class VehicleDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_vehicle(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()
            ->for($user)
            ->create();

        $this->actingAs($user);

        Volt::test('vehicles.index')
            ->call('delete', $vehicle->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_vehicle(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $vehicle = Vehicle::factory()
            ->for($anotherUser)
            ->create();

        $this->actingAs($user);

        Volt::test('vehicles.index')
            ->call('delete', $vehicle->id)
            ->assertForbidden();

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
        ]);
    }
}