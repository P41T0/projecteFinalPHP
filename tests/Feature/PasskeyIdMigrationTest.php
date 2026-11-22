<?php

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Passkeys\Passkeys;
use Laravel\Passkeys\Support\WebAuthn;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Symfony\Component\Uid\Uuid;
use Webauthn\AuthenticatorData;
use Webauthn\CredentialRecord;
use Webauthn\TrustPath\EmptyTrustPath;
use Webauthn\U2FPublicKey;

test('passkey id migration converts numeric ids while preserving credentials and users', function () {
    $originalConnection = DB::getDefaultConnection();
    $connectionConfig = config("database.connections.{$originalConnection}");
    $connectionConfig['prefix'] = 'uuid_migration_test_';
    if ($connectionConfig['driver'] === 'sqlite') {
        $connectionConfig['database'] = ':memory:';
    }

    config(['database.connections.uuid_migration_test' => $connectionConfig]);
    config([
        'passkeys.relying_party_id' => 'localhost',
        'passkeys.allowed_origins' => ['http://localhost'],
        'passkeys.user_handle_secret' => 'migration-test-secret',
    ]);
    DB::setDefaultConnection('uuid_migration_test');

    try {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
        });

        $originalMigration = require database_path('migrations/2026_11_03_000000_create_passkeys_table.php');
        $migration = require database_path('migrations/2026_11_22_000001_convert_passkey_ids_to_uuid.php');
        $originalMigration->up();

        $userId = (string) Str::uuid();
        DB::table('users')->insert([
            'id' => $userId,
            'name' => 'Passkey user',
            'email' => 'passkey@example.test',
            'password' => 'not-used',
        ]);
        $user = User::query()->findOrFail($userId);
        $privateKey = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']);
        expect($privateKey)->not->toBeFalse();
        $keyDetails = openssl_pkey_get_details($privateKey);
        expect($keyDetails)->not->toBeFalse();
        $publicKey = "\x04".str_pad($keyDetails['ec']['x'], 32, "\0", STR_PAD_LEFT)
            .str_pad($keyDetails['ec']['y'], 32, "\0", STR_PAD_LEFT);
        $rawId = random_bytes(32);
        $credentialId = Base64UrlSafe::encodeUnpadded($rawId);
        $credential = CredentialRecord::create(
            publicKeyCredentialId: $rawId,
            type: 'public-key',
            transports: [],
            attestationType: 'none',
            trustPath: EmptyTrustPath::create(),
            aaguid: Uuid::v4(),
            credentialPublicKey: U2FPublicKey::convertToCoseKey($publicKey),
            userHandle: $user->getPasskeyUserHandle(),
            counter: 0,
        );
        $credentialJson = WebAuthn::toJson($credential);
        foreach ([1, 42, 99, 1000] as $id) {
            DB::table('passkeys')->insert([
                'id' => $id,
                'user_id' => $userId,
                'name' => 'Legacy device',
                'credential_id' => $id === 42 ? $credentialId : "legacy-credential-{$id}",
                'credential' => $id === 42 ? $credentialJson : json_encode(['example' => 'preserved']),
            ]);
        }

        $updatesBeforeFailure = 2;
        DB::listen(function (QueryExecuted $query) use (&$updatesBeforeFailure) {
            if ($updatesBeforeFailure > 0
                && $query->connectionName === 'uuid_migration_test'
                && str_starts_with(strtolower($query->sql), 'update ')
                && str_contains($query->sql, 'passkeys')
                && --$updatesBeforeFailure === 0) {
                throw new RuntimeException('Simulated migration failure');
            }
        });
        expect(fn () => $migration->up())->toThrow(RuntimeException::class, 'Simulated migration failure');
        foreach ([1, 42, 99, 1000] as $id) {
            expect(DB::table('passkeys')->where('id', (string) $id)->exists())->toBeTrue();
        }

        $migration->up();

        expect(Passkeys::passkeyModel())->toBe(Passkey::class);
        $legacyRecords = Passkey::query()->get();
        expect($legacyRecords)->toHaveCount(4);
        foreach ($legacyRecords as $legacyRecord) {
            expect(Str::isUuid($legacyRecord->id, 7))->toBeTrue();
        }

        $legacy = $legacyRecords->firstWhere('credential_id', $credentialId);
        expect(Str::isUuid($legacy->id, 7))->toBeTrue()
            ->and(Passkey::query()->find('42'))->toBeNull()
            ->and($legacy->user_id)->toBe($userId)
            ->and($legacy->credential)->toBe(json_decode($credentialJson, true, flags: JSON_THROW_ON_ERROR))
            ->and((new Passkey)->resolveRouteBinding($legacy->id)->id)->toBe($legacy->id);

        $challenge = $this->getJson(route('passkey.login-options'))->assertOk()->json('options.challenge');
        $clientData = json_encode([
            'type' => 'webauthn.get',
            'challenge' => $challenge,
            'origin' => 'http://localhost',
        ], JSON_THROW_ON_ERROR);
        $authenticatorData = hash('sha256', 'localhost', true)
            .chr(AuthenticatorData::FLAG_UP | AuthenticatorData::FLAG_UV).pack('N', 1);
        $signature = '';
        expect(openssl_sign(
            $authenticatorData.hash('sha256', $clientData, true),
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256,
        ))->toBeTrue();

        $this->postJson(route('passkey.login'), [
            'credential' => [
                'id' => $credentialId,
                'rawId' => $credentialId,
                'type' => 'public-key',
                'response' => [
                    'clientDataJSON' => Base64UrlSafe::encodeUnpadded($clientData),
                    'authenticatorData' => Base64UrlSafe::encodeUnpadded($authenticatorData),
                    'signature' => Base64UrlSafe::encodeUnpadded($signature),
                    'userHandle' => Base64UrlSafe::encodeUnpadded($user->getPasskeyUserHandle()),
                ],
            ],
        ])->assertOk();
        $this->assertAuthenticatedAs($user);
        expect($legacy->fresh()->last_used_at)->not->toBeNull()
            ->and($legacy->fresh()->credential['counter'])->toBe(1);

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
