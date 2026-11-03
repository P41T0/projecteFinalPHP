<?php

use App\Models\User;

test('selected locale is retained for authentication and settings pages', function (string $locale, string $loginText, string $settingsText) {
    $this->get('/lang/'.$locale);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee($loginText);

    $this->actingAs(User::factory()->create())
        ->get(route('appearance.edit'))
        ->assertOk()
        ->assertSee($settingsText)
        ->assertSee($locale === 'ca' ? 'Clar' : 'Claro');
})->with([
    'Catalan' => ['ca', 'Inicia sessió al teu compte', 'Aparença'],
    'Spanish' => ['es', 'Inicia sesión en tu cuenta', 'Apariencia'],
]);

test('language switcher is available in navigation and account settings', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('inici'))
        ->assertOk()
        ->assertSee('Language · EN')
        ->assertSee('Català')
        ->assertSee('English')
        ->assertSee('Español')
        ->assertSee(route('localization', ['idioma' => 'ca']))
        ->assertSee(route('localization', ['idioma' => 'en']))
        ->assertSee(route('localization', ['idioma' => 'es']));

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee(__('Manage your profile and account settings'))
        ->assertSee('Language · EN')
        ->assertSee('Català')
        ->assertSee('Español');
});

test('language switcher changes and retains the selected locale', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('profile.edit'));
    $this->get(route('localization', ['idioma' => 'es']))
        ->assertRedirect(route('profile.edit', absolute: false));

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Idioma · ES')
        ->assertSee('Actualiza tu nombre y dirección de correo electrónico');
});
