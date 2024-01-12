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
    return redirect()->route('inici');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/contacte',[IniciController::class,"contacte"])->middleware(['auth'])->name('contacte');

//idioma
Route::get('/lang/{idioma}', 'App\Http\Controllers\LocalizationController@index')->where('idioma', 'ca|en|es');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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
//CRUD Productes
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



//CRUD seccions  
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

require __DIR__ . '/auth.php';
