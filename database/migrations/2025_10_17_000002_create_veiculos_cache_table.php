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
        Schema::create('veiculos_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->longText('result_json')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->timestamp('started_at')->nullable();
            $table->text('error_message')->nullable();
            
            // Informações sobre busca alternativa
            $table->string('local_original')->nullable();
            $table->string('local_alternativo')->nullable();
            $table->string('distancia_km')->nullable();
            
            $table->timestamps();
            
            $table->index('cache_key');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos_cache');
    }
};
