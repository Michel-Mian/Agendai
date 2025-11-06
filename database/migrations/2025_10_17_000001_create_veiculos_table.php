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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id('pk_id_veiculo');
            $table->unsignedBigInteger('fk_id_viagem');
            $table->foreign('fk_id_viagem')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
            
            // Informações básicas do veículo
            $table->string('nome_veiculo');
            $table->string('categoria')->nullable();
            $table->string('imagem_url', 1024)->nullable();
            
            // Configurações do veículo
            $table->integer('passageiros')->nullable();
            $table->string('malas')->nullable();
            $table->boolean('ar_condicionado')->default(false);
            $table->string('cambio')->nullable();
            $table->string('quilometragem')->nullable();
            
            // Proteções incluídas
            $table->text('diferenciais')->nullable(); // JSON array
            $table->text('tags')->nullable(); // JSON array
            
            // Local de retirada
            $table->string('endereco_retirada')->nullable();
            $table->string('tipo_local')->nullable();
            $table->string('nome_local')->nullable();
            
            // Locadora
            $table->string('locadora_nome')->nullable();
            $table->string('locadora_logo', 1024)->nullable();
            $table->decimal('avaliacao_locadora', 3, 1)->nullable();
            
            // Preço e link
            $table->decimal('preco_total', 10, 2)->nullable();
            $table->decimal('preco_diaria', 10, 2)->nullable();
            $table->string('link_reserva', 1024)->nullable();
            
            // Flag de seleção
            $table->boolean('is_selected')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
