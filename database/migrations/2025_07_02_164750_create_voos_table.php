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
        Schema::create('voos', function (Blueprint $table) {
            $table->bigIncrements('pk_id_voo');
            $table->text('desc_aeronave_voo')->nullable(false);
            $table->dateTime('data_hora_partida')->nullable(false);
            $table->dateTime('data_hora_chegada')->nullable(false);
            $table->string('origem_voo', 100)->nullable(false);
            $table->string('destino_voo', 100)->nullable(false);
            $table->string('companhia_voo', 100)->nullable(false);
            $table->float('preco_voo')->nullable(false);
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
        Schema::dropIfExists('voos');
    }
};
