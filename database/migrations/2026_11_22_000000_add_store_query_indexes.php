<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producte', function (Blueprint $table) {
            $table->index(['seccio_id', 'mostra_prod']);
        });

        Schema::table('comanda', function (Blueprint $table) {
            $table->index(['usuari_id', 'oberta']);
        });
    }

    public function down(): void
    {
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            // Foreign keys still need supporting indexes after the composite indexes are dropped.
            if (! Schema::hasIndex('comanda', ['usuari_id'])) {
                Schema::table('comanda', function (Blueprint $table) {
                    $table->index('usuari_id');
                });
            }

            if (! Schema::hasIndex('producte', ['seccio_id'])) {
                Schema::table('producte', function (Blueprint $table) {
                    $table->index('seccio_id');
                });
            }
        }

        Schema::table('comanda', function (Blueprint $table) {
            $table->dropIndex(['usuari_id', 'oberta']);
        });

        Schema::table('producte', function (Blueprint $table) {
            $table->dropIndex(['seccio_id', 'mostra_prod']);
        });
    }
};
