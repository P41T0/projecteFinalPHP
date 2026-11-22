<?php

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use App\Models\Seccio;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('seeded application records use uuid version 7 ids and foreign keys', function () {
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
                expect(Str::isUuid($record->{$column}, 7))->toBeTrue();
            }
        }
    }

    $producte = Producte::query()->with(['Seccio', 'Botiga'])->firstOrFail();
    $comanda = Comanda::query()->with(['usuari', 'productes'])->firstOrFail();

    expect(Str::isUuid($producte->getKey(), 7))->toBeTrue()
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

    expect(Str::isUuid($estocId, 7))->toBeTrue();

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

    expect(Str::isUuid($orderLineId, 7))->toBeTrue();
});

test('demo seeding is repeatable without replacing existing records', function () {
    Artisan::call('db:seed');

    $tables = ['users', 'seccio', 'producte', 'botiga', 'comanda', 'estoc_botiga', 'linia_comanda'];
    $ids = [];

    foreach ($tables as $table) {
        $ids[$table] = DB::table($table)->orderBy('id')->pluck('id')->all();
        expect($ids[$table])->not->toBeEmpty();
    }

    Artisan::call('db:seed');

    foreach ($tables as $table) {
        expect(DB::table($table)->orderBy('id')->pluck('id')->all())->toBe($ids[$table]);
    }

    $this->assertDatabaseHas('seccio', ['nom' => 'Telefonia mòbil']);
});

test('demo seeding leaves a populated database unchanged', function () {
    $user = User::factory()->create();

    Artisan::call('db:seed');

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('users', ['id' => $user->id, 'admin' => false]);
    $this->assertDatabaseCount('seccio', 0);
    $this->assertDatabaseCount('producte', 0);
});

test('existing uuid version 4 user ids remain usable', function () {
    $id = (string) Str::uuid();
    $user = User::factory()->create(['id' => $id]);

    expect(Str::isUuid($id, 4))->toBeTrue()
        ->and(User::query()->findOrFail($id)->id)->toBe($id)
        ->and(Str::isUuid(User::factory()->create()->id, 7))->toBeTrue();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('inventory rejects references to missing products and stores', function () {
    expect(fn () => DB::table('estoc_botiga')->insert([
        'id' => (string) Str::uuid(),
        'producte_id' => (string) Str::uuid(),
        'botiga_id' => (string) Str::uuid(),
        'quantitat' => 1,
    ]))->toThrow(QueryException::class);
});
