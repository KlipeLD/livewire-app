<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('kalkulator-paliwa', 'automotive.fuel-calculator')->name('fuel-calculator');

    Volt::route('moj-garaz', 'vehicles.index')->name('vehicles.index');
    Volt::route('moj-garaz/dodaj', 'vehicles.create')->name('vehicles.create');    
    Volt::route('moj-garaz/{vehicle}/edytuj', 'vehicles.edit')->name('vehicles.edit');

    Volt::route('moj-garaz/{vehicle}/serwis', 'service-records.index')->name('service-records.index');
    Volt::route('moj-garaz/{vehicle}/serwis/dodaj', 'service-records.create')->name('service-records.create');
    Volt::route('moj-garaz/{vehicle}/serwis/{serviceRecord}/edytuj','service-records.edit')->name('service-records.edit');

    Volt::route('moj-garaz/{vehicle}/tankowania', 'refuelings.index')->name('refuelings.index');
    Volt::route('moj-garaz/{vehicle}/tankowania/dodaj', 'refuelings.create')->name('refuelings.create');
    Volt::route('moj-garaz/{vehicle}/tankowania/{refueling}/edytuj', 'refuelings.edit')->name('refuelings.edit');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
