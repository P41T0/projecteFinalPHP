<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
 Schema::create('producte', function (Blueprint $table) {
  $table->id();
  $table->string('nom');
  $table->string('descripcio');
  $table->float('preu_unitari');
  $table->unsignedBigInteger('seccio_id');
  $table->foreign('seccio_id')->references('id')->on('seccio');
  $table->string('foto')->nullable();
  $table->boolean('mostra_prod');
  $table->timestamp('created_at')->useCurrent();
  $table->timestamp('updated_at')->useCurrent();
 });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producte');
    }
};
