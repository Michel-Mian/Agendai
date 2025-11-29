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
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viagem_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['user', 'assistant']); // user = usuário, assistant = IA
            $table->text('content'); // Conteúdo da mensagem
            $table->timestamps();

            // Foreign keys
            $table->foreign('viagem_id')->references('pk_id_viagem')->on('viagens')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Índices para melhor performance
            $table->index(['viagem_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};
