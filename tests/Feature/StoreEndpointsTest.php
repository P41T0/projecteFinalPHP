<?php

use App\Models\Botiga;
use App\Models\Comanda;
use App\Models\Producte;
use App\Models\Seccio;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

test('the storefront loads visible products with a single query for multiple sections', function () {
    $first = endpointSection(['nom_en' => 'First section']);
    $second = endpointSection(['nom_en' => 'Second section']);
    $hidden = endpointSection(['nom_en' => 'Hidden section', 'mostra_sec' => false]);
    endpointProduct($first, ['nom_en' => 'First product']);
    endpointProduct($second, ['nom_en' => 'Second product']);
    endpointProduct($first, ['nom_en' => 'Hidden product', 'mostra_prod' => false]);
    endpointProduct($hidden, ['nom_en' => 'Product in hidden section']);

    DB::enableQueryLog();
    DB::flushQueryLog();
    $response = $this->get(route('inici'));
    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    $response->assertOk()
        ->assertSee('First product')
        ->assertSee('Second product')
        ->assertDontSee('Hidden product')
        ->assertDontSee('Hidden section')
        ->assertDontSee('Product in hidden section');

    $productQueries = collect($queries)->filter(
        fn (array $query) => preg_match('/\bfrom\s+[`"]?producte[`"]?/i', $query['query'])
    );
    expect($productQueries)->toHaveCount(1);
    expect($productQueries->first()['query'])->toMatch('/\bmostra_prod\b/i');
});

test('the cart looks up an open order without loading closed orders', function () {
    $section = endpointSection();
    $product = endpointProduct($section);
    $store = endpointStore();
    $user = User::factory()->create();

    foreach ([false, false, true] as $open) {
        $order = new Comanda;
        $order->usuari_id = $user->id;
        $order->botiga_id = $store->id;
        $order->oberta = $open;
        $order->save();
    }

    DB::enableQueryLog();
    DB::flushQueryLog();
    $response = $this->actingAs($user)->get(route('comprar', $product));
    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    $response->assertOk();
    $this->assertDatabaseHas('linia_comanda', ['comanda_id' => $order->id, 'producte_id' => $product->id]);
    $orderQueries = collect($queries)->filter(
        fn (array $query) => preg_match('/\bfrom\s+[`"]?comanda[`"]?/i', $query['query'])
    );
    expect($orderQueries)->toHaveCount(1);
    expect($orderQueries->first()['query'])->toMatch('/\boberta\b/i')->toMatch('/\blimit\s+1\b/i');
});

test('editing a store only loads its associated products', function () {
    $section = endpointSection();
    $product = endpointProduct($section);
    $store = endpointStore();
    $store->productes()->attach($product->id, ['quantitat' => 3]);
    $admin = endpointAdmin();

    DB::enableQueryLog();
    DB::flushQueryLog();
    $response = $this->actingAs($admin)->get(route('botigues.edit', $store));
    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    $response->assertOk()->assertSee($product->nom);
    $productQueries = collect($queries)->filter(
        fn (array $query) => preg_match('/\bfrom\s+[`"]?producte[`"]?/i', $query['query'])
    );
    expect($productQueries)->toHaveCount(1);
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
        $this->get($url)
            ->assertNotFound()
            ->assertSee('404')
            ->assertSee(__('Pàgina no trobada'));
    }
    $this->put(route('seccions.store'))->assertNotFound();

    $this->actingAs(User::factory()->create())
        ->get(route('productes.select'))
        ->assertNotFound()
        ->assertSee(__('Pàgina no trobada'));
    $this->put(route('seccions.store'))->assertNotFound();

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

test('product image uploads reject disguised files and invalid image headers on create and update', function () {
    Storage::fake('public');
    $section = endpointSection();
    $product = endpointProduct($section, ['foto' => 'existing.png']);
    $this->actingAs(endpointAdmin());

    foreach ([
        ['image.png', '<script>alert(1)</script>', 'image/png'],
        ['image.gif', "GIF89a\x01\x00\x01\x00\x00\x00\x00", 'image/gif'],
        ['image.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>', 'image/svg+xml'],
    ] as [$name, $content, $claimedType]) {
        $temporary = UploadedFile::fake()->createWithContent($name, $content);

        foreach ([route('productes.store'), route('productes.update', $product)] as $url) {
            $upload = new UploadedFile($temporary->getRealPath(), $name, $claimedType, null, true);
            $this->putJson($url, endpointProductPayload($section->id, ['imatge' => $upload]))
                ->assertUnprocessable()
                ->assertJsonValidationErrors('imatge');
        }
    }

    $largeImage = UploadedFile::fake()->image('large.png')->size(2049);
    $this->putJson(route('productes.store'), endpointProductPayload($section->id, ['imatge' => $largeImage]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('imatge');

    foreach ([[4097, 1], [1, 4097]] as [$width, $height]) {
        $oversizedImage = UploadedFile::fake()->image('oversized.png', $width, $height);
        $this->putJson(route('productes.update', $product), endpointProductPayload($section->id, ['imatge' => $oversizedImage]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('imatge');
    }

    $this->assertDatabaseCount('producte', 1);
    expect($product->fresh()->foto)->toBe('existing.png')
        ->and(Storage::disk('public')->allFiles())->toBeEmpty();
});

test('product images use unique generated names and content-derived extensions', function () {
    Storage::fake('public');
    $section = endpointSection();
    $this->actingAs(endpointAdmin());
    $temporary = UploadedFile::fake()->image('source.png');

    $upload = new UploadedFile($temporary->getRealPath(), 'photo.php.jpg', 'image/jpeg', null, true);
    $this->put(route('productes.store'), endpointProductPayload($section->id, ['imatge' => $upload]))
        ->assertRedirect(route('productes.select'));
    $product = Producte::query()->where('nom', 'Carregador')->firstOrFail();
    $firstPath = $product->foto;
    Storage::disk('public')->assertExists($firstPath);
    expect($firstPath)->toMatch('/^[a-zA-Z0-9]{40}\.png$/')
        ->and(Storage::disk('public')->get($firstPath))->toBe(file_get_contents($temporary->getRealPath()));

    $upload = new UploadedFile($temporary->getRealPath(), 'photo.php.jpg', 'image/jpeg', null, true);
    $this->put(route('productes.update', $product), endpointProductPayload($section->id, ['imatge' => $upload]))
        ->assertRedirect(route('productes.select'));
    $secondPath = $product->fresh()->foto;
    Storage::disk('public')->assertExists($secondPath);
    expect($secondPath)->toMatch('/^[a-zA-Z0-9]{40}\.png$/')
        ->not->toBe($firstPath);
});

test('product uploads accept supported image formats and the maximum image dimensions', function () {
    Storage::fake('public');
    $section = endpointSection();
    $product = endpointProduct($section);
    $this->actingAs(endpointAdmin());

    foreach (['jpeg', 'png', 'gif', 'webp'] as $extension) {
        foreach ([[4096, 1], [1, 4096]] as [$width, $height]) {
            $upload = UploadedFile::fake()->image("image.{$extension}", $width, $height);
            $this->put(route('productes.update', $product), endpointProductPayload($section->id, ['imatge' => $upload]))
                ->assertRedirect(route('productes.select'))
                ->assertSessionHasNoErrors();
            Storage::disk('public')->assertExists($product->fresh()->foto);
        }
    }

    $chunk = fn (string $type, string $data): string => pack('N', strlen($data))
        .$type.$data.pack('N', crc32($type.$data));
    $rows = str_repeat("\0".str_repeat("\0", 512), 4096);
    $compressed = gzcompress($rows);
    unset($rows);
    $maximum = UploadedFile::fake()->createWithContent(
        'maximum.png',
        "\x89PNG\r\n\x1a\n"
            .$chunk('IHDR', pack('NNCCCCC', 4096, 4096, 1, 0, 0, 0, 0))
            .$chunk('IDAT', $compressed)
            .$chunk('IEND', '')
    );
    $this->put(route('productes.update', $product), endpointProductPayload($section->id, ['imatge' => $maximum]))
        ->assertRedirect(route('productes.select'))
        ->assertSessionHasNoErrors();
    Storage::disk('public')->assertExists($product->fresh()->foto);
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

    $otherProduct = endpointProduct($section, ['nom' => 'Accessori addicional']);
    $order->productes()->attach($otherProduct->id, ['quantitat' => 2]);

    $this->postJson(route('actualiza.quantitat', $order), [
        'productes' => [
            $product->id => '0',
            $otherProduct->id => 2,
        ],
        'botiga' => $store->id,
    ])->assertRedirect(route('comprarNP'));
    $this->assertDatabaseMissing('linia_comanda', [
        'comanda_id' => $order->id,
        'producte_id' => $product->id,
    ]);
    $this->assertDatabaseHas('linia_comanda', [
        'comanda_id' => $order->id,
        'producte_id' => $otherProduct->id,
        'quantitat' => 2,
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
