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
        Schema::table('diagnostics', function (Blueprint $table) {
            // Modifier le champ scoreglobal de INT à VARCHAR pour accepter les lettres (A, B, C)
            $table->string('scoreglobal', 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostics', function (Blueprint $table) {
            // Revenir à INT si nécessaire
            $table->integer('scoreglobal')->change();
        });
    }
};
