<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $distance = '';
    public $consumption = '';
    public $fuelPrice = '';

    public ?float $cost = null;

    public function calculate(): void
    {
        $validated = $this->validate([
            'distance' => ['required', 'numeric', 'min:1'],
            'consumption' => ['required', 'numeric', 'min:0.1'],
            'fuelPrice' => ['required', 'numeric', 'min:0.01'],
        ]);

        $liters = ($validated['distance'] / 100)
            * $validated['consumption'];

        $this->cost = round(
            $liters * $validated['fuelPrice'],
            2
        );
    }
}; ?>

<div class="mx-auto max-w-xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold">
            Kalkulator kosztu podróży
        </h1>

        <p class="text-sm text-zinc-600">
            Oblicz koszt paliwa na podstawie dystansu i spalania.
        </p>
    </div>

    <form wire:submit="calculate" class="space-y-4">
        <flux:input
            wire:model="distance"
            type="number"
            step="0.1"
            label="Dystans (km)"
        />

        <flux:input
            wire:model="consumption"
            type="number"
            step="0.1"
            label="Spalanie (l/100 km)"
        />

        <flux:input
            wire:model="fuelPrice"
            type="number"
            step="0.01"
            label="Cena paliwa (zł/l)"
        />

        <flux:button type="submit" variant="primary">
            Oblicz koszt
        </flux:button>
    </form>

    @if ($cost !== null)
        <div class="rounded-lg bg-green-100 p-4 text-green-900">
            Koszt podróży:
            <strong>
                {{ number_format($cost, 2, ',', ' ') }} zł
            </strong>
        </div>
    @endif
</div>