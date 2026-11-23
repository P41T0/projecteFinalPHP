<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('registration rejects invalid fields and duplicate email addresses', function () {
    $existing = User::factory()->create();

    $this->postJson(route('register.store'), [
        'name' => ['not a string'],
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'different',
        'admin' => true,
    ])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'password']);

    $this->postJson(route('register.store'), [
        'name' => 'Another user',
        'email' => $existing->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');

    $this->assertGuest();
    $this->assertDatabaseCount('users', 1);
});

test('registration does not allow assigning administrator privileges', function () {
    $this->post(route('register.store'), [
        'name' => 'New user',
        'email' => 'new@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'admin' => true,
    ])->assertSessionHasNoErrors();

    $user = User::where('email', 'new@example.test')->firstOrFail();
    expect($user->isAdmin())->toBeFalse();
});
