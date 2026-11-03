<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Features;
use Livewire\Livewire;

function createPasskeyFor(User $user, string $name = 'Test device'): int
{
    return DB::table('passkeys')->insertGetId([
        'user_id' => $user->id,
        'name' => $name,
        'credential_id' => fake()->unique()->sha256(),
        'credential' => json_encode([]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
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

    createPasskeyFor($user, 'My laptop');
    createPasskeyFor($otherUser, 'Someone else device');

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertOk()
        ->assertSee('My laptop')
        ->assertDontSee('Someone else device');
});

test('a user can delete only their own passkeys', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $passkeyId = createPasskeyFor($user);
    $otherPasskeyId = createPasskeyFor($otherUser);

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
