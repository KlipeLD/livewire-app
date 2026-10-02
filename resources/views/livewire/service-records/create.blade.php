<?php

use App\Models\Vehicle;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Vehicle $vehicle;

    public string $serviceDate = '';
    public string $mileage = '';
    public string $type = '';
    public string $description = '';
    public string $cost = '';
    public string $nextServiceDate = '';

    public function mount(Vehicle $vehicle): void
    {
        Gate::authorize('update', $vehicle);

        $this->vehicle = $vehicle;
        $this->serviceDate = now()->format('Y-m-d');
    }

    public function save(): void
    {
        Gate::authorize('update', $this->vehicle);

        $validated = $this->validate([
            'serviceDate' => ['required', 'date', 'before_or_equal:today'],
            'mileage' => ['required', 'integer', 'min:0', 'max:' . $this->vehicle->mileage],
            'type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'nextServiceDate' => ['nullable', 'date', 'after_or_equal:serviceDate'],
        ]);

        $this->vehicle->serviceRecords()->create([
            'service_date' => $validated['serviceDate'],
            'mileage' => $validated['mileage'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?: null,
            'cost' => $validated['cost'] === '' || $validated['cost'] === null ? null : $validated['cost'],
            'next_service_date' => $validated['nextServiceDate'] ?: null,
        ]);

        $this->redirectRoute(
            'service-records.index',
            ['vehicle' => $this->vehicle],
            navigate: true
        );
    }
}; ?>

<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold">
            Dodaj wpis serwisowy
        </h1>

        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ $vehicle->brand }} {{ $vehicle->model }}
            - {{ $vehicle->registration_number }}
        </p>
    </div>

    <form wire:submit="save" class="space-y-5">
        <flux:input wire:model="serviceDate" type="date" label="Data serwisu" />
        <flux:input wire:model="mileage" type="number" label="Przebieg podczas serwisu (km)" />
        <flux:input wire:model="type" label="Rodzaj serwisu" placeholder="np. Wymiana oleju" />
        <flux:textarea wire:model="description" label="Opis" rows="4" />
        <flux:input wire:model="cost" type="number" step="0.01" label="Koszt (zł)" />
        <flux:input wire:model="nextServiceDate" type="date" label="Data następnego serwisu (opcjonalnie)" />

        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">
                Zapisz
            </flux:button>

            <flux:button :href="route('service-records.index', $vehicle)" wire:navigate>
                Anuluj
            </flux:button>
        </div>
    </form>
</div>