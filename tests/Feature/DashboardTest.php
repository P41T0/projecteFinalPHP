<?php

use App\Models\Seccio;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated dashboard shows the same storefront as the home page', function () {
    $user = User::factory()->create();
    $user->forceFill(['email_verified_at' => now()])->save();
    $this->actingAs($user);

    $section = new Seccio;
    $section->nom = 'Secció compartida';
    $section->nom_es = 'Sección compartida';
    $section->nom_en = 'Shared storefront section';
    $section->descripcio = 'Descripció compartida';
    $section->descripcio_es = 'Descripción compartida';
    $section->descripcio_en = 'Shared storefront description';
    $section->mostra_sec = true;
    $section->save();

    $homeResponse = $this->get(route('inici'))->assertOk();
    $dashboardResponse = $this->get(route('dashboard'))->assertOk();

    $homeResponse->assertSee('Shared storefront section')
        ->assertSee('Shared storefront description');
    $dashboardResponse->assertSee('Shared storefront section')
        ->assertSee('Shared storefront description')
        ->assertDontSee("You're logged in!");
});

test('authenticated users see the contact link but not administration', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('contacte'))
        ->assertSee(__('Contacte'))
        ->assertDontSee(__('Administració'))
        ->assertDontSee('Laravel Starter Kit')
        ->assertDontSee('livewire-starter-kit')
        ->assertDontSee('starter-kits#livewire');
});

test('administrators see links to contact and administration', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['admin' => true])->save();
    $this->actingAs($admin);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('contacte'))
        ->assertSee(route('botigues.select'))
        ->assertSee(__('Contacte'))
        ->assertSee(__('Administració'))
        ->assertDontSee('Laravel Starter Kit')
        ->assertDontSee('livewire-starter-kit')
        ->assertDontSee('starter-kits#livewire');
});
