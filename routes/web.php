<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IniciController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\ProducteController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [IniciController::class, 'index'])->name('inici');
Route::get('/productes/{producte}', [IniciController::class, 'showProducte'])
    ->name('detall.producte');

Route::get('/comprar/{producte}', [ComandaController::class, 'afegir'])->middleware(['auth'])->name('comprar');
Route::get('/comprar', [ComandaController::class, 'afegir'])->middleware(['auth'])->name('comprarNP');
Route::post('/actualiza-quantitat/{comanda}', [ComandaController::class, 'canviQuantitat'])->middleware(['auth'])->name('actualiza.quantitat');
Route::get('/confcompra/{comanda}', [ComandaController::class, 'confirmar'])->middleware(['auth'])->name('confirma.compres');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



//idioma
Route::get('/lang/{idioma}', 'App\Http\Controllers\LocalizationController@index')->where('idioma', 'ca|en|es');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// CRUD
Route::get('/dashboard/productes/{producte}/edit', [App\Http\Controllers\ProducteController::class, 'edit'])
->middleware(['auth','admin'])
->name('productes.edit');
Route::put('/dashboard/productes/{producte}', [App\Http\Controllers\ProducteController::class, 'update'])
    ->middleware(['auth','admin'])
    ->name('productes.update');


require __DIR__.'/auth.php';
