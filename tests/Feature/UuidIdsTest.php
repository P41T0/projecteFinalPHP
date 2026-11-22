<?php

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use App\Models\Seccio;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base64UrlSafe;

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

test('legacy identifiers are migrated without breaking relationships or changing uuid7 identifiers', function () {
    $ids = [];
    foreach (['users', 'seccio', 'producte', 'botiga', 'comanda', 'estoc_botiga', 'linia_comanda', 'passkeys'] as $table) {
        $ids[$table] = (string) Str::uuid();
    }

    $user = User::factory()->create(['id' => $ids['users']]);
    $unchanged = User::factory()->create();
    $withoutPasskey = User::factory()->create(['id' => (string) Str::uuid()]);
    $handle = random_bytes(32);
    DB::table('seccio')->insert([
        'id' => $ids['seccio'], 'nom' => 'Section', 'nom_es' => 'Section', 'nom_en' => 'Section',
        'descripcio' => 'Section', 'descripcio_es' => 'Section', 'descripcio_en' => 'Section', 'mostra_sec' => true,
    ]);
    DB::table('producte')->insert([
        'id' => $ids['producte'], 'seccio_id' => $ids['seccio'], 'nom' => 'Product', 'nom_es' => 'Product',
        'nom_en' => 'Product', 'descripcio' => 'Product', 'descripcio_es' => 'Product',
        'descripcio_en' => 'Product', 'preu_unitari' => 1, 'mostra_prod' => true,
    ]);
    DB::table('botiga')->insert([
        'id' => $ids['botiga'], 'poblacio' => 'Store', 'adreca' => 'Address',
        'correu' => 'store@example.test', 'telefon' => '123456789',
    ]);
    DB::table('comanda')->insert([
        'id' => $ids['comanda'], 'usuari_id' => $ids['users'], 'botiga_id' => $ids['botiga'],
    ]);
    DB::table('estoc_botiga')->insert([
        'id' => $ids['estoc_botiga'], 'producte_id' => $ids['producte'], 'botiga_id' => $ids['botiga'], 'quantitat' => 3,
    ]);
    DB::table('linia_comanda')->insert([
        'id' => $ids['linia_comanda'], 'producte_id' => $ids['producte'], 'comanda_id' => $ids['comanda'], 'quantitat' => 2,
    ]);
    DB::table('passkeys')->insert([
        'id' => $ids['passkeys'], 'user_id' => $ids['users'], 'name' => 'Device',
        'credential_id' => 'credential', 'credential' => json_encode(['userHandle' => Base64UrlSafe::encodeUnpadded($handle)]),
    ]);
    DB::table('sessions')->insert([
        'id' => 'old-session', 'user_id' => $ids['users'], 'payload' => '', 'last_activity' => time(),
    ]);

    $migration = require database_path('migrations/2026_11_22_000002_migrate_application_ids_to_uuid7.php');
    $failOnce = true;
    DB::listen(function (QueryExecuted $query) use (&$failOnce) {
        if ($failOnce && str_starts_with(strtolower($query->sql), 'update ') && str_contains($query->sql, 'producte')) {
            $failOnce = false;
            throw new RuntimeException('Simulated identifier migration failure');
        }
    });
    expect(fn () => $migration->up())->toThrow(RuntimeException::class, 'Simulated identifier migration failure');
    foreach ($ids as $table => $oldId) {
        expect(DB::table($table)->where('id', $oldId)->exists())->toBeTrue();
    }
    expect(DB::table('sessions')->where('id', 'old-session')->exists())->toBeTrue();

    $migration->up();
    $migration->up();

    foreach ($ids as $table => $oldId) {
        expect(DB::table($table)->where('id', $oldId)->exists())->toBeFalse();
        foreach (DB::table($table)->pluck('id') as $id) {
            expect(Str::isUuid($id, 7))->toBeTrue();
        }
    }

    $migrated = User::query()->where('email', $user->email)->firstOrFail();
    $order = Comanda::query()->with(['usuari', 'productes'])->firstOrFail();
    $product = Producte::query()->with(['Seccio', 'Botiga'])->firstOrFail();
    expect($migrated->getPasskeyUserHandle())->toBe($handle)
        ->and(User::query()->findOrFail($unchanged->id)->passkey_user_handle)->toBeNull()
        ->and(User::query()->where('email', $withoutPasskey->email)->firstOrFail()->passkey_user_handle)->toBeNull()
        ->and($order->usuari->id)->toBe($migrated->id)
        ->and($order->productes->first()->id)->toBe($product->id)
        ->and($order->botiga_id)->toBe($product->Botiga->first()->id)
        ->and($product->Seccio->nom)->toBe('Section')
        ->and($migrated->passkeys()->firstOrFail()->credential_id)->toBe('credential')
        ->and(DB::table('sessions')->where('id', 'old-session')->exists())->toBeFalse();

    expect(fn () => $migration->down())->toThrow(LogicException::class);
});

test('inventory rejects references to missing products and stores', function () {
    expect(fn () => DB::table('estoc_botiga')->insert([
        'id' => (string) Str::uuid7(),
        'producte_id' => (string) Str::uuid7(),
        'botiga_id' => (string) Str::uuid7(),
        'quantitat' => 1,
    ]))->toThrow(QueryException::class);
});
