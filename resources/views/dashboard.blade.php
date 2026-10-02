@php
    use App\Models\Refueling;
    use App\Models\ServiceRecord;
    use App\Models\Vehicle;

    $userId = auth()->id();

    $vehiclesCount = Vehicle::query()
        ->where('user_id', $userId)
        ->count();

    $totalMileage = Vehicle::query()
        ->where('user_id', $userId)
        ->sum('mileage');

    $refuelings = Refueling::query()
        ->whereHas('vehicle', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })
        ->get();

    $refuelingsCount = $refuelings->count();

    $fuelCost = $refuelings->sum(function (Refueling $refueling): float {
        return (float) $refueling->liters * (float) $refueling->price_per_liter;
    });

    $serviceRecordsCount = ServiceRecord::query()
        ->whereHas('vehicle', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })
        ->count();

    $serviceCost = ServiceRecord::query()
        ->whereHas('vehicle', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })
        ->sum('cost');

    $latestRefueling = Refueling::query()
        ->with('vehicle')
        ->whereHas('vehicle', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })
        ->latest('refueled_at')
        ->first();

    $latestServiceRecord = ServiceRecord::query()
        ->with('vehicle')
        ->whereHas('vehicle', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })
        ->latest('service_date')
        ->first();
@endphp

<x-layouts.app>
    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold">
                Dashboard
            </h1>

            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Podsumowanie Twojego garażu.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Samochody
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $vehiclesCount }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Tankowania
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $refuelingsCount }}
                </p>
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Wpisy serwisowe
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $serviceRecordsCount }}
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Łączny przebieg
                </p>

                <p class="mt-2 text-2xl font-bold">
                    {{ number_format($totalMileage, 0, ',', ' ') }} km
                </p>
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Koszt paliwa
                </p>

                <p class="mt-2 text-2xl font-bold">
                    {{ number_format($fuelCost, 2, ',', ' ') }} zł
                </p>
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Koszt serwisów
                </p>

                <p class="mt-2 text-2xl font-bold">
                    {{ number_format((float) $serviceCost, 2, ',', ' ') }} zł
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="text-lg font-semibold">
                    Ostatnie tankowanie
                </h2>

                @if ($latestRefueling)
                    <p class="mt-3">
                        {{ $latestRefueling->vehicle->brand }}
                        {{ $latestRefueling->vehicle->model }}
                    </p>

                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $latestRefueling->refueled_at->format('d.m.Y') }}
                        ·
                        {{ number_format((float) $latestRefueling->liters, 2, ',', ' ') }} l
                    </p>
                @else
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Nie masz jeszcze żadnych tankowań.
                    </p>
                @endif
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="text-lg font-semibold">
                    Ostatni serwis
                </h2>

                @if ($latestServiceRecord)
                    <p class="mt-3">
                        {{ $latestServiceRecord->vehicle->brand }}
                        {{ $latestServiceRecord->vehicle->model }}
                    </p>

                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $latestServiceRecord->service_date->format('d.m.Y') }}
                        ·
                        {{ $latestServiceRecord->type }}
                    </p>
                @else
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">
                        Nie masz jeszcze żadnych wpisów serwisowych.
                    </p>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <flux:button
                variant="primary"
                :href="route('vehicles.index')"
                wire:navigate
            >
                Przejdź do garażu
            </flux:button>

            <flux:button
                :href="route('fuel-calculator')"
                wire:navigate
            >
                Kalkulator paliwa
            </flux:button>
        </div>
    </div>
</x-layouts.app>