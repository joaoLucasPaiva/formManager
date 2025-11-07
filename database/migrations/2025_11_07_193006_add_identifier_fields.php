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
        // Adiciona campo is_identifier na tabela form_fields
        Schema::table('form_fields', function (Blueprint $table) {
            $table->boolean('is_identifier')->default(false)->after('active');
        });

        // Adiciona campo identifier_value na tabela form_submissions
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('identifier_value')->nullable()->after('form_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('is_identifier');
        });

        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn('identifier_value');
        });
    }
};
