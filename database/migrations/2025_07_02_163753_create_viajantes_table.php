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
        Schema::create('viajantes', function (Blueprint $table) {
            $table->bigIncrements('pk_id_viajante');
            $table->string('nome', 100)->nullable();
            $table->tinyinteger('idade')->nullable(false);
            $table->unsignedBigInteger('responsavel_viajante_id')->nullable(true);
            $table->unsignedBigInteger('fk_id_viagem')->nullable(false);
            $table->timestamps();
            $table->foreign('fk_id_viagem')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajantes');
    }
};
