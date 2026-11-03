<?php

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use App\Models\Seccio;
use App\Models\User;
use Illuminate\Http\UploadedFile;

function endpointSection(array $overrides = []): Seccio
{
    $section = new Seccio;
    $section->nom = 'Telefonia';
    $section->nom_es = 'Telefonía';
    $section->nom_en = 'Phones';
    $section->descripcio = 'Telèfons';
    $section->descripcio_es = 'Teléfonos';
    $section->descripcio_en = 'Phones';
    $section->mostra_sec = true;
    foreach ($overrides as $attribute => $value) {
        $section->{$attribute} = $value;
    }
    $section->save();

    return $section;
}

function endpointProduct(Seccio $section, array $overrides = []): Producte
{
    $product = new Producte;
    $product->nom = 'Telèfon';
    $product->nom_es = 'Teléfono';
    $product->nom_en = 'Phone';
    $product->descripcio = 'Un telèfon de prova';
    $product->descripcio_es = 'Un teléfono de prueba';
    $product->descripcio_en = 'A test phone';
    $product->preu_unitari = 100;
    $product->seccio_id = $section->id;
    $product->mostra_prod = true;
    foreach ($overrides as $attribute => $value) {
        $product->{$attribute} = $value;
    }
    $product->save();

    return $product;
}

function endpointStore(): Botiga
{
    $store = new Botiga;
    $store->poblacio = 'Vic';
    $store->adreca = 'Carrer Major 1';
    $store->telefon = '938000000';
    $store->correu = 'vic@example.test';
    $store->save();

    return $store;
}

function endpointAdmin(): User
{
    $admin = User::factory()->create();
    $admin->forceFill(['admin' => true])->save();

    return $admin;
}

function endpointSectionPayload(array $overrides = []): array
{
    return array_replace([
        'nom' => 'Accessoris',
        'nomEs' => 'Accesorios',
        'nomEn' => 'Accessories',
        'descripcio' => 'Accessoris de telefonia',
        'descripcioEs' => 'Accesorios de telefonía',
        'descripcioEn' => 'Phone accessories',
    ], $overrides);
}

function endpointProductPayload(string $sectionId, array $overrides = []): array
{
    return array_replace([
        'nom' => 'Carregador',
        'nomEs' => 'Cargador',
        'nomEn' => 'Charger',
        'descripcio' => 'Carregador ràpid',
        'descripcioEs' => 'Cargador rápido',
        'descripcioEn' => 'Fast charger',
        'preu' => '19.99',
        'seccio' => $sectionId,
    ], $overrides);
}

test('public pages render populated sections, products, and stock', function () {
    $section = endpointSection();
    $product = endpointProduct($section);
    $store = endpointStore();
    $store->productes()->attach($product->id, ['quantitat' => 3]);

    $this->get(route('inici'))
        ->assertOk()
        ->assertSee($section->nom_en)
        ->assertSee($product->nom_en);

    $this->get(route('detall.producte', $product))
        ->assertOk()
        ->assertSee($product->nom_en)
        ->assertSee($store->poblacio)
        ->assertSee('3');

    $this->get('/productes/not-a-uuid')->assertNotFound();

    $this->actingAs(User::factory()->create())
        ->get(route('contacte'))
        ->assertOk();
});

test('admin pages render populated models and are restricted to administrators', function () {
    $section = endpointSection();
    $product = endpointProduct($section);
    $store = endpointStore();
    $store->productes()->attach($product->id, ['quantitat' => 3]);
    $adminRoutes = [
        route('botigues.select'),
        route('botigues.edit', $store),
        route('productes.select'),
        route('productes.create'),
        route('productes.edit', $product),
        route('seccions.select'),
        route('seccions.create'),
        route('seccions.edit', $section),
    ];

    foreach ($adminRoutes as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }

    $this->actingAs(User::factory()->create())
        ->get(route('productes.select'))
        ->assertForbidden();

    $this->actingAs(endpointAdmin());

    foreach ($adminRoutes as $url) {
        $this->get($url)->assertOk();
    }
});

test('section endpoints accept valid values and reject invalid values', function () {
    $admin = endpointAdmin();
    $this->actingAs($admin);

    $this->put(route('seccions.store'), endpointSectionPayload())
        ->assertRedirect(route('seccions.select'));

    $section = Seccio::query()->where('nom', 'Accessoris')->firstOrFail();
    $this->assertDatabaseHas('seccio', ['id' => $section->id, 'nom_en' => 'Accessories']);

    $this->putJson(route('seccions.store'), endpointSectionPayload(['nom' => '', 'descripcio' => str_repeat('x', 256)]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['nom', 'descripcio']);

    $this->assertDatabaseCount('seccio', 1);

    $this->put(route('seccions.update', $section), endpointSectionPayload(['nom' => 'Actualitzada']))
        ->assertRedirect(route('seccions.select'));
    $this->assertDatabaseHas('seccio', ['id' => $section->id, 'nom' => 'Actualitzada']);
});

test('product endpoints validate prices, images, and section ids before writing', function () {
    $admin = endpointAdmin();
    $section = endpointSection();
    $this->actingAs($admin);

    $this->put(route('productes.store'), endpointProductPayload($section->id))
        ->assertRedirect(route('productes.select'));

    $product = Producte::query()->where('nom', 'Carregador')->firstOrFail();
    $this->assertDatabaseHas('producte', [
        'id' => $product->id,
        'seccio_id' => $section->id,
        'preu_unitari' => 19.99,
    ]);

    $this->putJson(route('productes.store'), endpointProductPayload('not-a-uuid', [
        'nom' => '',
        'preu' => '-1',
    ]))->assertUnprocessable()
        ->assertJsonValidationErrors(['nom', 'preu', 'seccio']);
    $this->assertDatabaseCount('producte', 1);

    $this->put(route('productes.store'), endpointProductPayload($section->id, [
        'imatge' => UploadedFile::fake()->create('not-an-image.txt', 1, 'text/plain'),
    ]))->assertSessionHasErrors('imatge');
    $this->assertDatabaseCount('producte', 1);

    $this->put(route('productes.update', $product), endpointProductPayload($section->id, [
        'nom' => 'Actualitzat',
    ]))->assertRedirect(route('productes.select'));
    $this->assertDatabaseHas('producte', ['id' => $product->id, 'nom' => 'Actualitzat']);
});

test('inventory endpoint only accepts nonnegative quantities for products in that store', function () {
    $admin = endpointAdmin();
    $section = endpointSection();
    $product = endpointProduct($section);
    $otherProduct = endpointProduct($section, ['nom' => 'Altre producte']);
    $store = endpointStore();
    $store->productes()->attach($product->id, ['quantitat' => 2]);
    $this->actingAs($admin);

    $this->put(route('botigues.update', $store), [
        'productes' => [$product->id => 8],
    ])->assertRedirect(route('botigues.select'));
    $this->assertDatabaseHas('estoc_botiga', [
        'botiga_id' => $store->id,
        'producte_id' => $product->id,
        'quantitat' => 8,
    ]);

    $this->putJson(route('botigues.update', $store), [
        'productes' => [$product->id => -1],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(["productes.{$product->id}"]);

    $this->putJson(route('botigues.update', $store), [
        'productes' => [$otherProduct->id => 5],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['productes']);

    $this->assertDatabaseHas('estoc_botiga', [
        'botiga_id' => $store->id,
        'producte_id' => $product->id,
        'quantitat' => 8,
    ]);
});

test('cart endpoints require authentication, enforce ownership, and validate quantities', function () {
    $section = endpointSection();
    $product = endpointProduct($section);
    $store = endpointStore();
    $owner = User::factory()->create();

    $this->get(route('comprar', $product))
        ->assertRedirect(route('login'));

    $this->actingAs($owner)
        ->get(route('comprar', $product))
        ->assertOk()
        ->assertSee($product->nom_en);

    $order = $owner->comandes()->firstOrFail();
    $this->assertDatabaseHas('linia_comanda', [
        'comanda_id' => $order->id,
        'producte_id' => $product->id,
        'quantitat' => 1,
    ]);

    $this->postJson(route('actualiza.quantitat', $order), [
        'productes' => [$product->id => 4],
        'botiga' => $store->id,
    ])->assertRedirect(route('comprarNP'));
    $this->assertDatabaseHas('linia_comanda', [
        'comanda_id' => $order->id,
        'producte_id' => $product->id,
        'quantitat' => 4,
    ]);

    $this->postJson(route('actualiza.quantitat', $order), [
        'productes' => [$product->id => 11],
        'botiga' => $store->id,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(["productes.{$product->id}"]);

    $otherUser = User::factory()->create();
    $otherOrder = new Comanda;
    $otherOrder->usuari_id = $otherUser->id;
    $otherOrder->botiga_id = $store->id;
    $otherOrder->save();
    $otherOrder->productes()->attach($product->id, ['quantitat' => 1]);

    $this->postJson(route('actualiza.quantitat', $otherOrder), [
        'productes' => [$product->id => 2],
        'botiga' => $store->id,
    ])->assertForbidden();

    $this->get(route('confirma.compres', $otherOrder))->assertForbidden();

    $this->get(route('confirma.compres', $order))->assertOk();
    $this->assertDatabaseHas('comanda', ['id' => $order->id, 'oberta' => false]);
});

test('locale endpoint accepts supported locales and rejects unknown locales', function () {
    $this->get('/lang/ca')->assertRedirect();
    $this->get('/lang/fr')->assertNotFound();
});
