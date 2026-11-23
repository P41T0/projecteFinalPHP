<?php

use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('a guest cannot replay an authenticated profile update', function () {
    $user = User::factory()->create();
    $page = $this->actingAs($user)->get(route('profile.edit'))->assertOk();
    preg_match_all('/wire:snapshot="([^"]+)"/', $page->getContent(), $matches);

    $snapshot = null;
    foreach ($matches[1] as $encoded) {
        $candidate = html_entity_decode($encoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $data = json_decode($candidate, true);
        if (($data['memo']['name'] ?? null) === 'pages::settings.profile') {
            $snapshot = $candidate;
            break;
        }
    }
    expect($snapshot)->not->toBeNull();

    $this->post(route('logout'))->assertRedirect();
    $this->withHeader('X-Livewire', 'true')
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => ['name' => 'Changed', 'email' => 'changed@example.test'],
                'calls' => [['path' => '', 'method' => 'updateProfileInformation', 'params' => []]],
            ]],
        ])->assertUnauthorized();

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
});

test('profile updates require authentication and reject invalid or duplicate data', function () {
    $this->get(route('profile.edit'))->assertRedirect(route('login'));

    $user = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('updateProfileInformation')
        ->assertHasErrors(['name', 'email']);

    Livewire::test('pages::settings.profile')
        ->set('name', 'Valid name')
        ->set('email', $other->email)
        ->call('updateProfileInformation')
        ->assertHasErrors(['email']);

    expect($user->fresh()->name)->toBe($user->name)
        ->and($user->fresh()->email)->toBe($user->email);
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
