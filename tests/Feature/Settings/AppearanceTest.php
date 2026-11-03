<?php

use App\Models\User;

test('appearance settings support the system theme preference', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('appearance.edit'))
        ->assertOk()
        ->assertSee(__('Appearance'))
        ->assertSee(__('System'))
        ->assertSee('window.matchMedia')
        ->assertDontSee('class="dark"');
});
