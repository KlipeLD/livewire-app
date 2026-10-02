<?php

namespace App\Livewire\Forms;

use App\Enums\FuelType;
use App\Models\Vehicle;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Livewire\Form;

class VehicleForm extends Form
{
    public ?Vehicle $vehicle = null;

    public string $brand = '';
    public string $model = '';
    public string $year = '';
    public string $mileage = '';
    public string $registrationNumber = '';
    public FuelType $fuelType = FuelType::Petrol;

    protected function rules(): array
    {
        return [
            'brand' => [
                'required',
                'string',
                'max:100',
            ],
            'model' => [
                'required',
                'string',
                'max:100',
            ],
            'year' => [
                'required',
                'integer',
                'min:1886',
                'max:' . now()->year,
            ],
            'mileage' => [
                'required',
                'integer',
                'min:0',
            ],
            'registrationNumber' => [
                'required',
                'string',
                'max:20',
                Rule::unique(
                    'vehicles',
                    'registration_number'
                )
                    ->where(
                        fn (Builder $query) => $query->where(
                            'user_id',
                            auth()->id()
                        )
                    )
                    ->ignore($this->vehicle),
            ],
            'fuelType' => [
                'required',
                Rule::enum(FuelType::class),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'brand.required' => 'Podaj markę samochodu.',
            'model.required' => 'Podaj model samochodu.',
            'year.required' => 'Podaj rok produkcji.',
            'year.max' => 'Rok produkcji nie może być z przyszłości.',
            'mileage.required' => 'Podaj przebieg.',
            'mileage.min' => 'Przebieg nie może być ujemny.',
            'registrationNumber.required' => 'Podaj numer rejestracyjny.',
            'registrationNumber.unique' => 'Masz już pojazd z tym numerem.',
        ];
    }

    public function setVehicle(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->brand = $vehicle->brand;
        $this->model = $vehicle->model;
        $this->year = (string) $vehicle->year;
        $this->mileage = (string) $vehicle->mileage;
        $this->registrationNumber = $vehicle->registration_number;
        $this->fuelType = $vehicle->fuel_type;
    }

    public function store(): Vehicle
    {
        $this->registrationNumber = strtoupper(
            preg_replace('/\s+/u', '', $this->registrationNumber)
        );

        $validated = $this->validate();

        return auth()->user()->vehicles()->create(
            $this->databaseData($validated)
        );
    }

    public function update(): void
    {
        $this->registrationNumber = strtoupper(
            preg_replace('/\s+/u', '', $this->registrationNumber)
        );

        $validated = $this->validate();

        $this->vehicle->update(
            $this->databaseData($validated)
        );
    }

    private function databaseData(array $validated): array
    {
        return [
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'year' => $validated['year'],
            'mileage' => $validated['mileage'],
            'registration_number' => strtoupper(
                $validated['registrationNumber']
            ),
            'fuel_type' => $validated['fuelType'],
        ];
    }
}