<?php

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;
use Livewire\Livewire;

function createPasskeyFor(User $user, string $name = 'Test device'): string
{
    return $user->passkeys()->create([
        'name' => $name,
        'credential_id' => fake()->unique()->sha256(),
        'credential' => [],
    ])->id;
}

beforeEach(function () {
    if (! Features::canManagePasskeys()) {
        $this->markTestSkipped('Passkeys are not enabled.');
    }
});

test('passkey login option routes are registered and login page offers passkey sign in', function () {
    expect(Route::has('passkey.login-options'))->toBeTrue()
        ->and(Route::has('passkey.login'))->toBeTrue();

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Sign in with a passkey');
});

test('passkey security settings require authentication and password confirmation', function () {
    $this->get(route('security.edit'))
        ->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('security.edit'))
        ->assertRedirect(route('password.confirm'));
});

test('passkey security settings list only the authenticated user passkeys', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $passkeyId = createPasskeyFor($user, 'My laptop');
    createPasskeyFor($otherUser, 'Someone else device');

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertOk()
        ->assertSee('My laptop')
        ->assertSee("confirmDelete('{$passkeyId}')", false)
        ->assertDontSee('Someone else device');
});

test('a user can delete only their own passkeys', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $passkeyId = createPasskeyFor($user);
    $otherPasskeyId = createPasskeyFor($otherUser);

    expect(Str::isUuid($passkeyId, 7))->toBeTrue();

    Livewire::actingAs($user)
        ->test('pages::settings.security')
        ->call('confirmDelete', $otherPasskeyId)
        ->assertStatus(404);

    $this->assertDatabaseHas('passkeys', ['id' => $otherPasskeyId]);

    Livewire::actingAs($user)
        ->test('pages::settings.security')
        ->call('confirmDelete', $passkeyId)
        ->call('deletePasskey')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('passkeys', ['id' => $passkeyId]);
});

test('deleting a user cascades to their passkeys', function () {
    $user = User::factory()->create();
    $passkeyId = createPasskeyFor($user);

    $user->delete();

    $this->assertDatabaseMissing('passkeys', ['id' => $passkeyId]);
});
