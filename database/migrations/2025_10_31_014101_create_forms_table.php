<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            // público-alvo (use string p/ compatibilidade ampla)
            $table->string('audience', 32)->default('company'); // company | inspector
            $table->boolean('is_published')->default(false);
            $table->boolean('is_locked')->default(false); // trava edição após publicar
            $table->timestamp('published_at')->nullable();
            // jsonb é suportado no Postgres; se usar outro SGBD, pode trocar para json()
            $table->jsonb('meta')->nullable(); // ex: cores, logo, configs extras
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
