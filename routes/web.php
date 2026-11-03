<?php

use App\Http\Controllers\BotigaController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\IniciController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\ProducteController;
use App\Http\Controllers\SeccioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IniciController::class, 'index'])->name('inici');
Route::get('/productes/{producte}', [IniciController::class, 'showProducte'])
    ->name('detall.producte');

Route::get('/comprar/{producte}', [ComandaController::class, 'afegir'])->middleware(['auth'])->name('comprar');
Route::get('/comprar', [ComandaController::class, 'afegir'])->middleware(['auth'])->name('comprarNP');
Route::post('/actualiza-quantitat/{comanda}', [ComandaController::class, 'canviQuantitat'])->middleware(['auth'])->name('actualiza.quantitat');
Route::get('/confcompra/{comanda}', [ComandaController::class, 'confirmar'])->middleware(['auth'])->name('confirma.compres');

Route::get('/dashboard', [IniciController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/contacte', [IniciController::class, 'contacte'])->middleware(['auth'])->name('contacte');

// idioma
Route::get('/lang/{idioma}', [LocalizationController::class, 'index'])->where('idioma', 'ca|en|es');

// CRUD Botigues
Route::get('/dashboard/botigues/{botiga}/edit', [BotigaController::class, 'edit'])
    ->middleware('admin')
    ->name('botigues.edit');
Route::get('/dashboard/modificaBotigues', [BotigaController::class, 'index'])
    ->middleware('admin')
    ->name('botigues.select');
Route::put('/dashboard/botigues/{botiga}', [BotigaController::class, 'update'])
    ->middleware('admin')
    ->name('botigues.update');
// CRUD Productes
Route::put('/dashboard/productes/{producte}', [ProducteController::class, 'update'])
    ->middleware('admin')
    ->name('productes.update');
Route::get('/dashboard/modificaProductes', [ProducteController::class, 'index'])
    ->middleware('admin')
    ->name('productes.select');
Route::get('/dashboard/creaProd', [ProducteController::class, 'create'])
    ->middleware('admin')
    ->name('productes.create');
Route::put('/dashboard/storeProd', [ProducteController::class, 'store'])
    ->middleware('admin')
    ->name('productes.store');
Route::get('/dashboard/modifElems')->middleware('admin')->name('modifElems');
Route::get('/dashboard/productes/{producte}/edit', [ProducteController::class, 'edit'])
    ->middleware('admin')
    ->name('productes.edit');

// CRUD seccions
Route::get('/dashboard/seccions/{seccio}/edit', [SeccioController::class, 'edit'])
    ->middleware('admin')
    ->name('seccions.edit');
Route::put('/dashboard/seccions/{seccio}', [SeccioController::class, 'update'])
    ->middleware('admin')
    ->name('seccions.update');
Route::put('/dashboard/storeSeccio', [SeccioController::class, 'store'])
    ->middleware('admin')
    ->name('seccions.store');
Route::get('/dashboard/modificaSeccions', [SeccioController::class, 'index'])
    ->middleware('admin')
    ->name('seccions.select');
Route::get('/dashboard/creaSeccio', [SeccioController::class, 'create'])
    ->middleware('admin')
    ->name('seccions.create');

require __DIR__.'/settings.php';
