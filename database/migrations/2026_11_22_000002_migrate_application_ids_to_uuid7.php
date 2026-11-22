<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base64UrlSafe;

return new class extends Migration
{
    public function up(): void
    {
        $references = [
            'users' => ['comanda' => 'usuari_id', 'passkeys' => 'user_id'],
            'seccio' => ['producte' => 'seccio_id'],
            'producte' => ['estoc_botiga' => 'producte_id', 'linia_comanda' => 'producte_id'],
            'botiga' => ['comanda' => 'botiga_id', 'estoc_botiga' => 'botiga_id'],
            'comanda' => ['linia_comanda' => 'comanda_id'],
            'estoc_botiga' => [],
            'linia_comanda' => [],
            'passkeys' => [],
        ];
        $driver = DB::getDriverName();
        if (! in_array($driver, ['mysql', 'sqlite'], true)) {
            foreach (array_keys($references) as $table) {
                foreach (DB::table($table)->pluck('id') as $id) {
                    if (! Str::isUuid((string) $id, 7)) {
                        throw new RuntimeException("UUID migration does not support database driver: {$driver}");
                    }
                }
            }
        }

        if (! Schema::hasColumn('users', 'passkey_user_handle')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('passkey_user_handle', 88)->nullable();
            });
        }

        // MySQL cannot defer foreign keys; migrations must run with the application stopped.
        if ($driver === 'mysql') {
            Schema::disableForeignKeyConstraints();
        }

        try {
            DB::transaction(function () use ($driver, $references) {
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA defer_foreign_keys = ON');
                }

                foreach ($references as $table => $children) {
                    foreach (DB::table($table)->pluck('id') as $id) {
                        $id = (string) $id;
                        if (Str::isUuid($id, 7)) {
                            continue;
                        }

                        $newId = (string) Str::uuid7();
                        $updates = ['id' => $newId];
                        if ($table === 'users') {
                            $credential = DB::table('passkeys')->where('user_id', $id)->value('credential');
                            if ($credential !== null) {
                                $record = json_decode($credential, true, flags: JSON_THROW_ON_ERROR);
                                if (! is_array($record) || ! isset($record['userHandle']) || ! is_string($record['userHandle'])) {
                                    throw new UnexpectedValueException('Passkey credential has no valid user handle.');
                                }
                                $handle = Base64UrlSafe::decodeNoPadding($record['userHandle']);
                                if ($handle === '' || strlen($handle) > 64) {
                                    throw new UnexpectedValueException('Passkey credential has an invalid user handle.');
                                }
                                $updates['passkey_user_handle'] = base64_encode($handle);
                            }
                            DB::table('sessions')->where('user_id', $id)->delete();
                        }

                        DB::table($table)->where('id', $id)->update($updates);
                        foreach ($children as $child => $column) {
                            DB::table($child)->where($column, $id)->update([$column => $newId]);
                        }
                    }
                }
            });
        } finally {
            if ($driver === 'mysql') {
                Schema::enableForeignKeyConstraints();
            }
        }
    }

    public function down(): void
    {
        if (DB::table('users')->whereNotNull('passkey_user_handle')->exists()) {
            throw new LogicException('Cannot remove preserved passkey user handles.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('passkey_user_handle');
        });
    }
};
