<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('linia_comanda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producte_id');
            $table->foreign('producte_id')->references('id')->on('producte');
            $table->unsignedBigInteger('comanda_id');
            $table->foreign('comanda_id')->references('id')->on('comanda');
            $table->integer('quantitat');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linia_comanda');
    }
};
