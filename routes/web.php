<?php
use App\Http\Controllers\IniciController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\ProducteController;


use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', [IniciController::class, 'index'])->name('home');
Route::get('/productes/{producte}', [IniciController::class, 'showProducte'])
    ->name('detall.producte');

Route::get('/comprar/{producte}', [ComandaController::class, 'afegir'])->middleware(['auth'])->name('comprar');
Route::get('/comprar', [ComandaController::class, 'afegir'])->middleware(['auth'])->name('comprarNP');
Route::post('/actualiza-quantitat/{comanda}', [ComandaController::class, 'canviQuantitat'])->middleware(['auth'])->name('actualiza.quantitat');
Route::get('/confcompra/{comanda}', [ComandaController::class, 'confirmar'])->middleware(['auth'])->name('confirma.compres');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
