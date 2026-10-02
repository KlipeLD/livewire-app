<?php

use App\Livewire\Forms\VehicleForm;
use App\Models\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Gate;

new #[Layout('components.layouts.app')] class extends Component {
    public Vehicle $vehicle;

    public VehicleForm $form;

    public function mount(Vehicle $vehicle): void
    {
        Gate::authorize('update', $vehicle);

        $this->vehicle = $vehicle;

        $this->form->setVehicle($vehicle);
    }

    public function save(): void
    {
        Gate::authorize('update', $this->vehicle);

        $this->form->update();

        session()->flash(
            'status',
            'Dane samochodu zostały zaktualizowane.'
        );

        $this->redirectRoute(
            'vehicles.index',
            navigate: true
        );
    }
}; ?>

<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold">
            Edytuj samochód
        </h1>

        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            Zmień dane pojazdu.
        </p>
    </div>

    <form wire:submit="save" class="space-y-5">
        <flux:input
            wire:model="form.brand"
            label="Marka"
        />

        <flux:input
            wire:model="form.model"
            label="Model"
        />

        <flux:input
            wire:model="form.year"
            type="number"
            label="Rok produkcji"
        />

        <flux:input
            wire:model="form.mileage"
            type="number"
            label="Przebieg (km)"
        />

        <flux:input
            wire:model="form.registrationNumber"
            label="Numer rejestracyjny"
        />

        <flux:select
            wire:model="form.fuelType"
            label="Rodzaj paliwa"
        >
            <flux:select.option value="petrol">
                Benzyna
            </flux:select.option>

            <flux:select.option value="diesel">
                Diesel
            </flux:select.option>

            <flux:select.option value="hybrid">
                Hybryda
            </flux:select.option>

            <flux:select.option value="electric">
                Elektryczny
            </flux:select.option>
        </flux:select>

        <div class="flex gap-3">
            <flux:button
                type="submit"
                variant="primary"
            >
                Zapisz zmiany
            </flux:button>

            <flux:button
                :href="route('vehicles.index')"
                wire:navigate
            >
                Anuluj
            </flux:button>
        </div>
    </form>
</div>