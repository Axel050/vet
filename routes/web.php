<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::livewire('dashboard', 'pages::dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas de Administración
Route::middleware(['auth', 'verified', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::livewire('veterinarias', 'pages::admin.veterinarias')->name('veterinarias');

    Route::livewire('especies', 'pages::admin.especies')->name('especies');

    Route::livewire('razas', 'pages::admin.breeds')->name('razas');

    Route::livewire('precios-planes', 'pages::admin.plan-prices')->name('plan-prices');

});

Route::middleware(['auth', 'verified', 'veterinaria_active'])->prefix('veterinaria')->name('veterinaria.')->group(function () {
    Route::livewire('clientes', 'pages::admin.veterinaria.customers')->name('customers');
    Route::livewire('tipos', 'pages::admin.veterinaria.types')->name('types');
    Route::livewire('historial', 'pages::admin.veterinaria.medical-records')->name('records');

    // Pro Features
    Route::livewire('perfil', 'pages::admin.veterinaria.veterinary-profile-editor')->name('profile.editor');
});

Route::livewire('clinica/{veterinary:slug}', 'pages::public.veterinary-landing')->name('public.veterinary');

Route::livewire('historial/{veterinary:slug}/{pet}/{token}', 'pages::public.pet-history-public')->name('public.history');

Route::get('historial/{veterinary:slug}/{pet}/{token}/pdf', [\App\Http\Controllers\MedicalHistoryPdfController::class, 'download'])->name('public.history.pdf');

require __DIR__.'/settings.php';
