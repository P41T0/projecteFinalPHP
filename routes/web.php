<?php

use App\Http\Controllers\ComandaController;
use App\Http\Controllers\IniciController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', [IniciController::class, 'index'])->name('inici');
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

Route::get('/contacte', [IniciController::class, 'contacte'])->middleware(['auth'])->name('contacte');

// idioma
Route::get('/lang/{idioma}', 'App\Http\Controllers\LocalizationController@index')->where('idioma', 'ca|en|es');

// CRUD Botigues
Route::get('/dashboard/botigues/{botiga}/edit', [App\Http\Controllers\BotigaController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('botigues.edit');
Route::get('/dashboard/modificaBotigues', [App\Http\Controllers\BotigaController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('botigues.select');
Route::put('/dashboard/botigues/{botiga}', [App\Http\Controllers\BotigaController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('botigues.update');
// CRUD Productes
Route::put('/dashboard/productes/{producte}', [App\Http\Controllers\ProducteController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('productes.update');
Route::get('/dashboard/modificaProductes', [App\Http\Controllers\ProducteController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('productes.select');
Route::get('/dashboard/creaProd', [App\Http\Controllers\ProducteController::class, 'create'])
    ->middleware(['auth', 'admin'])
    ->name('productes.create');
Route::put('/dashboard/storeProd', [App\Http\Controllers\ProducteController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('productes.store');
Route::get('/dashboard/modifElems')->middleware(['auth', 'admin'])->name('modifElems');
Route::get('/dashboard/productes/{producte}/edit', [App\Http\Controllers\ProducteController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('productes.edit');

// CRUD seccions
Route::get('/dashboard/seccions/{seccio}/edit', [App\Http\Controllers\SeccioController::class, 'edit'])
    ->middleware(['auth', 'admin'])
    ->name('seccions.edit');
Route::put('/dashboard/seccions/{seccio}', [App\Http\Controllers\SeccioController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('seccions.update');
Route::put('/dashboard/storeSeccio', [App\Http\Controllers\SeccioController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('seccions.store');
Route::get('/dashboard/modificaSeccions', [App\Http\Controllers\SeccioController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('seccions.select');
Route::get('/dashboard/creaSeccio', [App\Http\Controllers\SeccioController::class, 'create'])
    ->middleware(['auth', 'admin'])
    ->name('seccions.create');

require __DIR__.'/settings.php';
