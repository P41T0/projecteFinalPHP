<?php

use Illuminate\Support\Str;

test('cache does not deserialize arbitrary classes', function () {
    expect(config('cache.serializable_classes'))->toBeFalse();
});

test('public filesystem url is built from the application url', function () {
    expect(config('filesystems.disks.public.url'))
        ->toBe(rtrim((string) (config('app.url') ?: 'http://localhost'), '/').'/storage');
});

test('laravel 13 cache redis and session prefixes use their current defaults', function () {
    $applicationPrefix = Str::slug(config('app.name'));

    expect(config('cache.prefix'))->toBe($applicationPrefix.'-cache-')
        ->and(config('database.redis.options.prefix'))->toBe($applicationPrefix.'-database-')
        ->and(config('session.cookie'))->toBe($applicationPrefix.'-session');
});
