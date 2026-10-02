<?php

namespace Tests\Feature\ServiceRecords;


use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_service_records_page_for_their_vehicle(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('service-records.index', $vehicle))
            ->assertOk();
    }

    public function test_user_can_create_service_record_for_their_vehicle(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'mileage' => 120000,
        ]);

        $this->actingAs($user);

        Volt::test('service-records.create', [
            'vehicle' => $vehicle,
        ])
            ->set('serviceDate', '2026-09-20 00:00:00')
            ->set('mileage', 118000)
            ->set('type', 'Wymiana oleju')
            ->set('description', 'Olej, filtr oleju, filtr powietrza')
            ->set('cost', 450)
            ->set('nextServiceDate', '2027-09-20 00:00:00')
            ->call('save')
            ->assertRedirect(route('service-records.index', $vehicle));

        $this->assertDatabaseHas('service_records', [
            'vehicle_id' => $vehicle->id,
            'service_date' => '2026-09-20 00:00:00',
            'mileage' => 118000,
            'type' => 'Wymiana oleju',
            'description' => 'Olej, filtr oleju, filtr powietrza',
            'cost' => 450,
            'next_service_date' => '2027-09-20 00:00:00',
        ]);
    }

    public function test_user_can_update_service_record_for_their_vehicle(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'mileage' => 150000,
        ]);

        $serviceRecord = ServiceRecord::factory()->create([
            'vehicle_id' => $vehicle->id,
            'service_date' => '2026-01-10',
            'mileage' => 100000,
            'type' => 'Stary wpis',
            'cost' => 300,
        ]);

        $this->actingAs($user);

        Volt::test('service-records.edit', [
            'vehicle' => $vehicle,
            'serviceRecord' => $serviceRecord,
        ])
            ->set('serviceDate', '2026-08-15')
            ->set('mileage', 140000)
            ->set('type', 'Wymiana klocków hamulcowych')
            ->set('description', 'Przód')
            ->set('cost', 800)
            ->set('nextServiceDate', null)
            ->call('save')
            ->assertRedirect(route('service-records.index', $vehicle));

        $this->assertDatabaseHas('service_records', [
            'id' => $serviceRecord->id,
            'vehicle_id' => $vehicle->id,
            'service_date' => '2026-08-15 00:00:00',
            'mileage' => 140000,
            'type' => 'Wymiana klocków hamulcowych',
            'description' => 'Przód',
            'cost' => 800,
            'next_service_date' => null,
        ]);
    }

    public function test_user_can_delete_service_record_for_their_vehicle(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
        ]);

        $serviceRecord = ServiceRecord::factory()->create([
            'vehicle_id' => $vehicle->id,
        ]);

        $this->actingAs($user);

        Volt::test('service-records.edit', [
            'vehicle' => $vehicle,
            'serviceRecord' => $serviceRecord,
        ])
            ->call('delete')
            ->assertRedirect(route('service-records.index', $vehicle));

        $this->assertDatabaseMissing('service_records', [
            'id' => $serviceRecord->id,
        ]);
    }

    public function test_user_cannot_access_service_records_for_someone_elses_vehicle(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get(route('service-records.index', $vehicle))
            ->assertForbidden();
    }

    public function test_service_record_requires_valid_data(): void
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Volt::test('service-records.create', [
            'vehicle' => $vehicle,
        ])
            ->set('serviceDate', '')
            ->set('mileage', '')
            ->set('type', '')
            ->set('cost', -10)
            ->call('save')
            ->assertHasErrors([
                'serviceDate' => 'required',
                'mileage' => 'required',
                'type' => 'required',
                'cost' => 'min',
            ]);
    }
}