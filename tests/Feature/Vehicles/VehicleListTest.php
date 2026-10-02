<?php

namespace Tests\Feature\Vehicles;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class VehicleListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_their_vehicles(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Vehicle::factory()->for($user)->create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
        ]);

        Vehicle::factory()->for($anotherUser)->create([
            'brand' => 'BMW',
            'model' => 'E46',
        ]);

        $this->actingAs($user);

        Volt::test('vehicles.index')
            ->assertSee('Toyota')
            ->assertSee('Corolla')
            ->assertDontSee('BMW')
            ->assertDontSee('E46');
    }

    public function test_user_can_search_for_a_vehicle(): void
    {
        $user = User::factory()->create();

        Vehicle::factory()->for($user)->create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
        ]);

        Vehicle::factory()->for($user)->create([
            'brand' => 'BMW',
            'model' => 'E46',
        ]);

        $this->actingAs($user);

        Volt::test('vehicles.index')
            ->set('search', 'Toyota')
            ->assertSee('Corolla')
            ->assertDontSee('E46');
    }
}