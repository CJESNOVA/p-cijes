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
        Schema::table('entreprises', function (Blueprint $table) {
            if (!Schema::hasColumn('entreprises', 'entrepriseprofil_id')) {
                $table->unsignedBigInteger('entrepriseprofil_id')->nullable()->default(0);
            }
            if (!Schema::hasColumn('entreprises', 'est_membre_cijes')) {
                $table->boolean('est_membre_cijes')->default(0);
            }
            if (!Schema::hasColumn('entreprises', 'annee_creation')) {
                $table->year('annee_creation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn(['entrepriseprofil_id', 'est_membre_cijes', 'annee_creation']);
        });
    }
};
