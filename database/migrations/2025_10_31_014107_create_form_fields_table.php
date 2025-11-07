<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();

            // redundância útil p/ filtros e integridade
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_section_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('label');
            $table->string('name');              // identificador "machine": employees_count
            $table->string('type', 32);          // text|textarea|number|select|radio|checkbox|date|datetime|file|email|tel|rating
            $table->boolean('required')->default(false);

            $table->jsonb('validation')->nullable(); // min, max, regex, in, etc.
            $table->jsonb('ui')->nullable();         // placeholder, help, mask, suffix, etc.

            $table->unsignedInteger('position')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['form_id', 'name']); // evita duplicar "name" dentro do mesmo form
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
