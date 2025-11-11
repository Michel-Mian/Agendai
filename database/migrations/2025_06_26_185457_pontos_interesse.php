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
        Schema::create('pontos_interesse', function (Blueprint $table) {
            $table->bigIncrements('pk_id_ponto_interesse');
            $table->date('data_ponto_interesse');
            $table->time('hora_ponto_interesse')->nullable();
            $table->text('desc_ponto_interesse')->nullable();
            $table->string('nome_ponto_interesse', 100);
            $table->string('placeid_ponto_interesse', 100);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('categoria', 100)->nullable();
            $table->timestamps();
            $table->boolean('is_completed')->nullable()->default(false);

            $table->unsignedBigInteger('fk_id_viagem');
            $table->foreign('fk_id_viagem')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pontos_interesse');
    }
};