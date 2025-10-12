<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguros', function (Blueprint $table) {
            $table->id('pk_id_seguro');
            $table->unsignedBigInteger('fk_id_viagem');
            $table->foreign('fk_id_viagem')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
            
            $table->string('seguradora');
            $table->string('plano');
            $table->text('detalhes_etarios')->nullable();
            $table->string('link', 1024)->nullable();

            $table->string('cobertura_medica')->nullable();
            $table->string('cobertura_bagagem')->nullable();

            $table->decimal('preco_pix', 10, 2)->nullable();
            $table->decimal('preco_cartao', 10, 2)->nullable();
            $table->string('parcelamento_cartao')->nullable();

            $table->unsignedBigInteger('fk_id_viajante');
            $table->foreign('fk_id_viajante')->references('pk_id_viajante')->on('viajantes')->onDelete('cascade');
            
            $table->boolean('is_selected')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('seguros');
    }
};