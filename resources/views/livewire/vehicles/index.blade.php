<?php

use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Gate;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function vehicles(): LengthAwarePaginator
    {
        return Vehicle::query()
            ->where('user_id', auth()->id())
            ->when(
                $this->search,
                function ($query): void {
                    $query->where(function ($query): void {
                        $query
                            ->where(
                                'brand',
                                'like',
                                '%' . $this->search . '%'
                            )
                            ->orWhere(
                                'model',
                                'like',
                                '%' . $this->search . '%'
                            )
                            ->orWhere(
                                'registration_number',
                                'like',
                                '%' . $this->search . '%'
                            );
                    });
                }
            )
            ->latest()
            ->paginate(10);
    }

    public function delete(int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        Gate::authorize('delete', $vehicle);

        $vehicle->delete();

        unset($this->vehicles);

        session()->flash(
            'status',
            'Samochód został usunięty.'
        );
    }
}; ?>

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">
                Mój garaż
            </h1>

            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Lista Twoich samochodów.
            </p>
        </div>

        <flux:button
            variant="primary"
            :href="route('vehicles.create')"
            wire:navigate
        >
            Dodaj samochód
        </flux:button>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-green-100 p-4 text-green-900">
            {{ session('status') }}
        </div>
    @endif

    <flux:input
        wire:model.live.debounce.300ms="search"
        label="Szukaj"
        placeholder="Marka, model lub numer rejestracyjny"
    />

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($this->vehicles as $vehicle)
            <div
                wire:key="vehicle-{{ $vehicle->id }}"
                class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">
                            {{ $vehicle->brand }}
                            {{ $vehicle->model }}
                        </h2>

                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $vehicle->registration_number }}
                        </p>
                    </div>

                    <span class="text-sm">
                        {{ $vehicle->year }}
                    </span>
                </div>

                <div class="mt-4 text-sm">
                    Przebieg:
                    <strong>
                        {{ number_format($vehicle->mileage, 0, ',', ' ') }}
                        km
                    </strong>
                </div>

                <div class="mt-5  flex gap-2">
                    <flux:button
                        size="sm"
                        :href="route('service-records.index', $vehicle)"
                        wire:navigate
                    >
                    Historia napraw
                    </flux:button>

                    <flux:button
                        size="sm"
                        :href="route('refuelings.index', $vehicle)"
                        wire:navigate
                    >
                        Tankowania
                    </flux:button>
                </div>

                

                <div class="mt-5 flex gap-2">
                    <flux:button
                        size="sm"
                        icon="pencil"
                        :href="route('vehicles.edit', $vehicle)"
                        wire:navigate
                    >
                        Edytuj
                    </flux:button>

                    <flux:button
                        size="sm"
                        icon="trash"
                        variant="danger"
                        wire:click="delete({{ $vehicle->id }})"
                        wire:confirm="Czy na pewno chcesz usunąć ten samochód?"
                    >
                        Usuń
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed p-8 text-center md:col-span-2">
                @if ($search !== '')
                    Nie znaleziono pasujących samochodów.
                @else
                    Nie masz jeszcze żadnego samochodu.
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $this->vehicles->links() }}
    </div>
</div>