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
        Schema::table('viagens', function (Blueprint $table) {
            // Campos para carro próprio
            $table->boolean('usa_carro_proprio')->default(false)->after('orcamento_viagem');
            $table->decimal('autonomia_veiculo_km_l', 5, 2)->nullable()->after('usa_carro_proprio'); // km/litro
            $table->string('tipo_combustivel', 50)->nullable()->after('autonomia_veiculo_km_l'); // gasolina, etanol, diesel, etc
            
            // Dados calculados pela API do Google Maps
            $table->decimal('distancia_total_km', 10, 2)->nullable()->after('tipo_combustivel');
            $table->decimal('pedagio_estimado', 10, 2)->nullable()->after('distancia_total_km');
            $table->decimal('combustivel_estimado_litros', 10, 2)->nullable()->after('pedagio_estimado');
            $table->decimal('custo_combustivel_estimado', 10, 2)->nullable()->after('combustivel_estimado_litros');
            $table->decimal('preco_combustivel_litro', 10, 2)->nullable()->after('custo_combustivel_estimado');
            $table->text('rota_detalhada')->nullable()->after('preco_combustivel_litro'); // JSON com informações da rota
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viagens', function (Blueprint $table) {
            $table->dropColumn([
                'usa_carro_proprio',
                'autonomia_veiculo_km_l',
                'tipo_combustivel',
                'distancia_total_km',
                'pedagio_estimado',
                'combustivel_estimado_litros',
                'custo_combustivel_estimado',
                'preco_combustivel_litro',
                'rota_detalhada'
            ]);
        });
    }
};
