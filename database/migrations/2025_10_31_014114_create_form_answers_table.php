<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_field_id')->constrained()->cascadeOnDelete();

            // múltiplas representações para consulta/relatório mais fácil
            $table->text('value')->nullable();           // texto "cru" (fallback)
            $table->jsonb('json_value')->nullable();     // arrays/estruturas (ex.: checkbox múltiplo)
            $table->decimal('number_value', 18, 6)->nullable();
            $table->date('date_value')->nullable();
            $table->timestamp('datetime_value')->nullable();

            $table->timestamps();

            $table->unique(['form_submission_id', 'form_field_id']); // 1 resposta/campo por submissão
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_answers');
    }
};
