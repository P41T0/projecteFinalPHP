<?php

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use App\Models\Seccio;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('seeded application records use uuid ids and foreign keys', function () {
    Artisan::call('db:seed');

    $uuidColumns = [
        'users' => ['id'],
        'seccio' => ['id'],
        'producte' => ['id', 'seccio_id'],
        'botiga' => ['id'],
        'comanda' => ['id', 'usuari_id', 'botiga_id'],
        'estoc_botiga' => ['id', 'producte_id', 'botiga_id'],
        'linia_comanda' => ['id', 'producte_id', 'comanda_id'],
    ];

    foreach ($uuidColumns as $table => $columns) {
        foreach (DB::table($table)->get($columns) as $record) {
            foreach ($columns as $column) {
                expect(Str::isUuid($record->{$column}))->toBeTrue();
            }
        }
    }

    $producte = Producte::query()->with(['Seccio', 'Botiga'])->firstOrFail();
    $comanda = Comanda::query()->with(['usuari', 'productes'])->firstOrFail();

    expect(Str::isUuid($producte->getKey()))->toBeTrue()
        ->and($producte->Seccio)->toBeInstanceOf(Seccio::class)
        ->and($producte->Botiga)->not->toBeEmpty()
        ->and($comanda->usuari)->toBeInstanceOf(User::class)
        ->and($comanda->productes)->not->toBeEmpty();

    $botiga = new Botiga;
    $botiga->poblacio = 'UUID test';
    $botiga->adreca = 'Test';
    $botiga->telefon = '000000000';
    $botiga->correu = 'uuid-test@example.com';
    $botiga->save();

    $producte->Botiga()->attach($botiga->id, ['quantitat' => 1]);
    $estocId = DB::table('estoc_botiga')
        ->where('botiga_id', $botiga->id)
        ->value('id');

    expect(Str::isUuid($estocId))->toBeTrue();

    $newProducte = new Producte;
    $newProducte->nom = 'Test';
    $newProducte->nom_es = 'Test';
    $newProducte->nom_en = 'Test';
    $newProducte->descripcio = 'Test';
    $newProducte->descripcio_es = 'Test';
    $newProducte->descripcio_en = 'Test';
    $newProducte->preu_unitari = 1;
    $newProducte->seccio_id = $producte->seccio_id;
    $newProducte->mostra_prod = false;
    $newProducte->save();

    $comanda->productes()->attach($newProducte->id, ['quantitat' => 1]);
    $orderLineId = DB::table('linia_comanda')
        ->where('comanda_id', $comanda->id)
        ->where('producte_id', $newProducte->id)
        ->value('id');

    expect(Str::isUuid($orderLineId))->toBeTrue();
});
