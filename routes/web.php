<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IniciController;
use App\Http\Controllers\ComandaController;

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
Route::get('/confcompra/{comanda}/{usuari}', [ComandaController::class, 'confirmar'])->middleware(['auth'])->name('confirma.compres');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
