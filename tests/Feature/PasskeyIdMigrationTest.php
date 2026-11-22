<?php

use App\Models\Passkey;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Passkeys\Passkeys;

test('passkey id migration converts numeric ids while preserving credentials and users', function () {
    $originalConnection = DB::getDefaultConnection();
    $connectionConfig = config("database.connections.{$originalConnection}");
    $connectionConfig['prefix'] = 'uuid_migration_test_';
    if ($connectionConfig['driver'] === 'sqlite') {
        $connectionConfig['database'] = ':memory:';
    }

    config(['database.connections.uuid_migration_test' => $connectionConfig]);
    DB::setDefaultConnection('uuid_migration_test');

    try {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
        });

        $originalMigration = require database_path('migrations/2026_11_03_000000_create_passkeys_table.php');
        $migration = require database_path('migrations/2026_11_22_000001_convert_passkey_ids_to_uuid.php');
        $originalMigration->up();

        $userId = (string) Str::uuid();
        DB::table('users')->insert(['id' => $userId]);
        foreach ([1, 42, 99, 1000] as $id) {
            DB::table('passkeys')->insert([
                'id' => $id,
                'user_id' => $userId,
                'name' => 'Legacy device',
                'credential_id' => "legacy-credential-{$id}",
                'credential' => json_encode(['example' => 'preserved']),
            ]);
        }

        $migration->up();

        expect(Passkeys::passkeyModel())->toBe(Passkey::class);
        $legacyRecords = Passkey::query()->get();
        expect($legacyRecords)->toHaveCount(4);
        foreach ($legacyRecords as $legacyRecord) {
            expect(Str::isUuid($legacyRecord->id, 7))->toBeTrue();
        }

        $legacy = $legacyRecords->firstWhere('credential_id', 'legacy-credential-42');
        expect(Str::isUuid($legacy->id, 7))->toBeTrue()
            ->and(Passkey::query()->find('42'))->toBeNull()
            ->and($legacy->user_id)->toBe($userId)
            ->and($legacy->credential)->toBe(['example' => 'preserved'])
            ->and((new Passkey)->resolveRouteBinding($legacy->id)->id)->toBe($legacy->id);

        $new = Passkey::query()->forceCreate([
            'user_id' => $userId,
            'name' => 'New device',
            'credential_id' => 'new-credential',
            'credential' => [],
        ]);
        expect(Str::isUuid($new->id, 7))->toBeTrue();

        expect(fn () => $migration->down())->toThrow(LogicException::class);
        expect(Passkey::query()->count())->toBe(5);

        Passkey::query()->delete();
        $migration->down();
        DB::table('passkeys')->insert([
            'id' => 42,
            'user_id' => $userId,
            'name' => 'Rolled back',
            'credential_id' => 'rollback-credential',
            'credential' => json_encode([]),
        ]);
        expect((string) DB::table('passkeys')->where('id', 42)->value('id'))->toBe('42');
        DB::table('users')->where('id', $userId)->delete();
        expect(DB::table('passkeys')->count())->toBe(0);
    } finally {
        Schema::dropIfExists('passkeys');
        Schema::dropIfExists('users');
        DB::setDefaultConnection($originalConnection);
        DB::purge('uuid_migration_test');
    }
});
