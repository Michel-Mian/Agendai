<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguros_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->json('result_json')->nullable();
            $table->timestamps();
            $table->timestamp('started_at')->nullable();
            $table->string('status')->default('no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguros_cache');
    }
};