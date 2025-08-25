<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Exemplo de criação da tabela viagens (ajuste conforme seu projeto)
        Schema::create('viagens', function (Blueprint $table) {
            $table->bigIncrements('pk_id_viagem');
            $table->string('destino_viagem');
            $table->string('origem_viagem');
            $table->date('data_inicio_viagem');
            $table->date('data_final_viagem');
            $table->decimal('orcamento_viagem', 12, 2)->nullable();
            $table->unsignedBigInteger('fk_id_usuario');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viagens');
    }
};
