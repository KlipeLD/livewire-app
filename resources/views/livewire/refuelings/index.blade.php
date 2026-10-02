<?php

use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public Vehicle $vehicle;

    public function mount(Vehicle $vehicle): void
    {
        Gate::authorize('view', $vehicle);

        $this->vehicle = $vehicle;
    }

    #[Computed]
    public function refuelings(): LengthAwarePaginator
    {
        return $this->vehicle
            ->refuelings()
            ->latest('refueled_at')
            ->paginate(10);
    }
}; ?>

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Historia tankowań</h1>

            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ $vehicle->brand }} {{ $vehicle->model }}
                - {{ $vehicle->registration_number }}
            </p>
        </div>

        <flux:button
            variant="primary"
            :href="route('refuelings.create', $vehicle)"
            wire:navigate
        >
            Dodaj tankowanie
        </flux:button>
    </div>

    <div class="space-y-4">
        @forelse ($this->refuelings as $refueling)
            <article
                wire:key="refueling-{{ $refueling->id }}"
                class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">
                            {{ $refueling->refueled_at->format('d.m.Y') }}
                        </h2>

                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ number_format($refueling->mileage, 0, ',', ' ') }} km
                            ·
                            {{ number_format((float) $refueling->liters, 2, ',', ' ') }} l
                        </p>
                    </div>

                    <p class="font-semibold">
                        {{ number_format((float) $refueling->liters * (float) $refueling->price_per_liter, 2, ',', ' ') }} zł
                    </p>
                </div>

                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                    Cena za litr:
                    {{ number_format((float) $refueling->price_per_liter, 2, ',', ' ') }} zł

                    @if ($refueling->full_tank)
                        · Do pełna
                    @endif
                </p>

                <div class="mt-4">
                    <flux:button
                        size="sm"
                        icon="pencil"
                        :href="route('refuelings.edit', [
                            'vehicle' => $vehicle,
                            'refueling' => $refueling,
                        ])"
                        wire:navigate
                    >
                        Edytuj
                    </flux:button>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed p-8 text-center">
                Nie ma jeszcze tankowań dla tego samochodu.
            </div>
        @endforelse
    </div>

    {{ $this->refuelings->links() }}
</div>