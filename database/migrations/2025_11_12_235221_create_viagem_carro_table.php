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
        Schema::create('viagem_carro', function (Blueprint $table) {
            $table->id('pk_id_viagem_carro');
            
            // Relacionamento com viagens (1:1)
            $table->unsignedBigInteger('fk_id_viagem')->unique();
            $table->foreign('fk_id_viagem')
                  ->references('pk_id_viagem')
                  ->on('viagens')
                  ->onDelete('cascade');
            
            // Dados do veículo
            $table->decimal('autonomia_veiculo_km_l', 5, 2)->comment('Autonomia do veículo em km/litro');
            $table->string('tipo_combustivel', 50)->comment('Tipo de combustível: gasolina, etanol, diesel, etc');
            $table->decimal('preco_combustivel_litro', 10, 2)->comment('Preço do combustível por litro');
            
            // Dados calculados pela API (Routes API)
            $table->decimal('distancia_total_km', 10, 2)->nullable()->comment('Distância total calculada pela API');
            $table->decimal('pedagio_estimado', 10, 2)->nullable()->comment('Valor de pedágios (oficial ou estimado)');
            $table->boolean('pedagio_oficial')->default(false)->comment('Se o valor do pedágio é oficial da API');
            $table->decimal('combustivel_estimado_litros', 10, 2)->nullable()->comment('Litros de combustível necessários');
            $table->decimal('custo_combustivel_estimado', 10, 2)->nullable()->comment('Custo total do combustível');
            $table->integer('duracao_segundos')->nullable()->comment('Duração da viagem em segundos');
            
            // Dados da rota (JSON)
            $table->text('rota_detalhada')->nullable()->comment('Informações detalhadas da rota (polyline, legs, etc) em JSON');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viagem_carro');
    }
};
