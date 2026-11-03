<?php

use Illuminate\Support\Facades\Route;

test('not found errors render the custom page', function () {
    $this->get('/page-that-does-not-exist')
        ->assertNotFound()
        ->assertSee('404')
        ->assertSee(__('Pàgina no trobada'))
        ->assertSee(__("Tornar a l'inici"));
});

test('http errors render their custom pages', function (int $status, string $title) {
    Route::get("/test-error-{$status}", fn () => abort($status));

    $this->get("/test-error-{$status}")
        ->assertStatus($status)
        ->assertSee((string) $status)
        ->assertSee(__($title));
})->with([
    [403, 'Accés denegat'],
    [500, 'S’ha produït un error'],
    [503, 'Tornem de seguida'],
]);
