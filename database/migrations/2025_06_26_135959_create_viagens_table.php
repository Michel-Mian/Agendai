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
        Schema::create('viagens', function (Blueprint $table) {
            $table->bigIncrements('pk_id_viagem');
            $table->string('nome_viagem', 100);
            $table->date('data_inicio_viagem');
            $table->date('data_final_viagem');
            $table->string('origem_viagem', 100) -> nullable();
            $table->decimal('orcamento_viagem', 10, 2)->nullable();
            $table->unsignedBigInteger('fk_id_usuario');
            $table->foreign('fk_id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viagens');
    }
};

// Todos os campos necessários estão presentes:
// - pk_id_viagem
// - destino_viagem, data_inicio_viagem, data_final_viagem, origem_viagem, orcamento_viagem
// - fk_id_usuario (relacionamento com users)
// - fk_id_seguro_selecionado (relacionamento com seguros, nullable)
// - fk_id_seguro_selecionado (relacionamento com seguros, nullable)
