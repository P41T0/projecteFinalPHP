<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Passkeys must use UUID strings consistently with the application's other models.
        Schema::table('passkeys', function (Blueprint $table) {
            $table->string('id', 36)->change();
        });

        // Replace only legacy numeric IDs while preserving credentials and their user references.
        DB::transaction(function () {
            foreach (DB::table('passkeys')->select('id')->cursor() as $passkey) {
                $id = (string) $passkey->id;

                if (ctype_digit($id)) {
                    DB::table('passkeys')->where('id', $id)->update(['id' => (string) Str::uuid7()]);
                }
            }
        });
    }

    public function down(): void
    {
        // Converted identifiers cannot be restored to their original values.
    }
};
