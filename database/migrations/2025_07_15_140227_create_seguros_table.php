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
        Schema::create('seguros', function (Blueprint $table) {
            $table->bigIncrements('pk_id_seguro');
            $table->string('site')->nullable();
            $table->string('preco')->nullable();
            $table->string('preco_pix')->nullable();
            $table->string('preco_cartao')->nullable();
            $table->string('parcelas')->nullable();
            $table->json('dados')->nullable();
            $table->string('link')->nullable();
            $table->string('cobertura_medica')->nullable();
            $table->string('cobertura_bagagem')->nullable();
            $table->string('cobertura_cancelamento')->nullable();
            $table->string('cobertura_odonto')->nullable();
            $table->string('cobertura_medicamentos')->nullable();
            $table->string('cobertura_eletronicos')->nullable();
            $table->string('cobertura_mochila_mao')->nullable();
            $table->string('cobertura_atraso_embarque')->nullable();
            $table->string('cobertura_pet')->nullable();
            $table->string('cobertura_sala_vip')->nullable();
            $table->boolean('cobertura_telemedicina')->nullable();
            $table->unsignedBigInteger('fk_id_viagem');
            $table->foreign('fk_id_viagem')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguros');
    }
};
