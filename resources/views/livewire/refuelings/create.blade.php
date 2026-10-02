<?php

use App\Models\Vehicle;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Vehicle $vehicle;

    public string $refueledAt = '';
    public string $mileage = '';
    public string $liters = '';
    public string $pricePerLiter = '';
    public bool $fullTank = true;

    public function mount(Vehicle $vehicle): void
    {
        Gate::authorize('update', $vehicle);

        $this->vehicle = $vehicle;
        $this->refueledAt = now()->format('Y-m-d');
    }

    public function save(): void
    {
        Gate::authorize('update', $this->vehicle);

        $validated = $this->validate([
            'refueledAt' => ['required', 'date', 'before_or_equal:today'],
            'mileage' => ['required', 'integer', 'min:0'],
            'liters' => ['required', 'numeric', 'min:0.001'],
            'pricePerLiter' => ['required', 'numeric', 'min:0.001'],
            'fullTank' => ['boolean'],
        ]);

        $this->vehicle->refuelings()->create([
            'refueled_at' => $validated['refueledAt'],
            'mileage' => $validated['mileage'],
            'liters' => $validated['liters'],
            'price_per_liter' => $validated['pricePerLiter'],
            'full_tank' => $validated['fullTank'],
        ]);

        $this->redirectRoute('refuelings.index', ['vehicle' => $this->vehicle], navigate: true);
    }
}; ?>

<div class="mx-auto max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold">Dodaj tankowanie</h1>

    <form wire:submit="save" class="space-y-5">
        <flux:input wire:model="refueledAt" type="date" label="Data tankowania" />
        <flux:input wire:model="mileage" type="number" label="Przebieg (km)" />
        <flux:input wire:model="liters" type="number" step="0.001" label="Liczba litrów" />
        <flux:input wire:model="pricePerLiter" type="number" step="0.001" label="Cena za litr (zł)" />
        <flux:checkbox wire:model="fullTank" label="Tankowanie do pełna" />

        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">Zapisz</flux:button>

            <flux:button :href="route('refuelings.index', $vehicle)" wire:navigate>
                Anuluj
            </flux:button>
        </div>
    </form>
</div>