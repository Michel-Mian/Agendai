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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id('pk_id_hotel');
            $table->string('nome_hotel')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->decimal('avaliacao', 2, 1)->nullable();
            $table->date('data_check_in')->nullable();
            $table->date('data_check_out')->nullable();
            $table->float('preco')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('fk_id_viagem');
            $table->foreign('fk_id_viagem')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
