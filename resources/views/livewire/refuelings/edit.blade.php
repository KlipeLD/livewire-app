<?php

use App\Models\Refueling;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Vehicle $vehicle;
    public Refueling $refueling;

    public string $refueledAt = '';
    public string $mileage = '';
    public string $liters = '';
    public string $pricePerLiter = '';
    public bool $fullTank = false;

    public function mount(Vehicle $vehicle, Refueling $refueling): void
    {
        Gate::authorize('update', $vehicle);

        $this->vehicle = $vehicle;

        $this->refueling = $vehicle
            ->refuelings()
            ->findOrFail($refueling->id);

        $this->refueledAt = $this->refueling->refueled_at->format('Y-m-d');
        $this->mileage = (string) $this->refueling->mileage;
        $this->liters = (string) $this->refueling->liters;
        $this->pricePerLiter = (string) $this->refueling->price_per_liter;
        $this->fullTank = (bool) $this->refueling->full_tank;
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

        $this->refueling->update([
            'refueled_at' => $validated['refueledAt'],
            'mileage' => $validated['mileage'],
            'liters' => $validated['liters'],
            'price_per_liter' => $validated['pricePerLiter'],
            'full_tank' => $validated['fullTank'],
        ]);

        session()->flash('status', 'Tankowanie zostało zaktualizowane.');

        $this->redirectRoute(
            'refuelings.index',
            ['vehicle' => $this->vehicle],
            navigate: true
        );
    }

    public function delete(): void
    {
        Gate::authorize('delete', $this->vehicle);

        $this->refueling->delete();

        session()->flash('status', 'Tankowanie zostało usunięte.');

        $this->redirectRoute(
            'refuelings.index',
            ['vehicle' => $this->vehicle],
            navigate: true
        );
    }
}; ?>

<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold">
            Edytuj tankowanie
        </h1>

        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ $vehicle->brand }} {{ $vehicle->model }}
            - {{ $vehicle->registration_number }}
        </p>
    </div>

    <form wire:submit="save" class="space-y-5">
        <flux:input
            wire:model="refueledAt"
            type="date"
            label="Data tankowania"
        />

        <flux:input
            wire:model="mileage"
            type="number"
            label="Przebieg (km)"
        />

        <flux:input
            wire:model="liters"
            type="number"
            step="0.001"
            label="Liczba litrów"
        />

        <flux:input
            wire:model="pricePerLiter"
            type="number"
            step="0.001"
            label="Cena za litr (zł)"
        />

        <flux:checkbox
            wire:model="fullTank"
            label="Tankowanie do pełna"
        />

        <div class="flex flex-wrap gap-3">
            <flux:button
                type="submit"
                variant="primary"
            >
                Zapisz zmiany
            </flux:button>

            <flux:button
                :href="route('refuelings.index', $vehicle)"
                wire:navigate
            >
                Anuluj
            </flux:button>

            <flux:button
                type="button"
                variant="danger"
                wire:click="delete"
                wire:confirm="Na pewno usunąć to tankowanie?"
            >
                Usuń
            </flux:button>
        </div>
    </form>
</div>