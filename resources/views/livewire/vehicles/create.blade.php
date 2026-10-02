<?php

use App\Livewire\Forms\VehicleForm;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public VehicleForm $form;

    public function save(): void
    {
        $this->form->store();

        session()->flash(
            'status',
            'Samochód został dodany.'
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
            Dodaj samochód
        </h1>

        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            Wprowadź podstawowe dane pojazdu.
        </p>
    </div>

    <form wire:submit="save" class="space-y-5">
        <flux:input
            wire:model="form.brand"
            label="Marka"
            placeholder="Toyota"
        />

        <flux:input
            wire:model="form.model"
            label="Model"
            placeholder="Corolla"
        />

        <flux:input
            wire:model="form.year"
            type="number"
            label="Rok produkcji"
            placeholder="2020"
        />

        <flux:input
            wire:model="form.mileage"
            type="number"
            label="Przebieg (km)"
            placeholder="75000"
        />

        <flux:input
            wire:model="form.registrationNumber"
            label="Numer rejestracyjny"
            placeholder="KR 12345"
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
            <flux:button type="submit" variant="primary">
                Dodaj samochód
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