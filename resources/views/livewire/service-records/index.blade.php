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
    public function records(): LengthAwarePaginator
    {
        return $this->vehicle
            ->serviceRecords()
            ->latest('service_date')
            ->paginate(10);
    }
}; ?>

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">
                Historia serwisowa
            </h1>

            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ $vehicle->brand }} {{ $vehicle->model }}
                — {{ $vehicle->registration_number }}
            </p>
        </div>

        <flux:button
            variant="primary"
            :href="route('service-records.create', $vehicle)"
            wire:navigate
        >
            Dodaj wpis
        </flux:button>
    </div>

    <div class="space-y-4">
        @forelse ($this->records as $record)
            <article
                wire:key="service-record-{{ $record->id }}"
                class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">
                            {{ $record->type }}
                        </h2>

                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $record->service_date->format('d.m.Y') }}
                            ·
                            {{ number_format($record->mileage, 0, ',', ' ') }} km
                        </p>
                    </div>

                    @if ($record->cost !== null)
                        <p class="font-semibold">
                            {{ number_format((float) $record->cost, 2, ',', ' ') }} zł
                        </p>
                    @endif
                </div>

                @if ($record->description)
                    <p class="mt-3">
                        {{ $record->description }}
                    </p>
                @endif

                @if ($record->next_service_date)
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Następny serwis:
                        {{ $record->next_service_date->format('d.m.Y') }}
                    </p>
                @endif

                <div class="mt-4">
                    <flux:button
                        size="sm"
                        icon="pencil"
                        :href="route('service-records.edit', [
                            'vehicle' => $vehicle,
                            'serviceRecord' => $record,
                        ])"
                        wire:navigate
                    >
                        Edytuj
                    </flux:button>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed p-8 text-center">
                Nie ma jeszcze wpisów serwisowych dla tego samochodu.
            </div>
        @endforelse
    </div>

    {{ $this->records->links() }}
</div>