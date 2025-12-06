<?php

test('returns a successful response', function () {
    $response = $this->get(route('inici'));

    $response->assertStatus(200);
});