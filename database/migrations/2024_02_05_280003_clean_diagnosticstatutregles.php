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
        Schema::table('diagnosticstatutregles', function (Blueprint $table) {
            // Supprimer la clé étrangère avec le bon nom
            $table->dropForeign('diagnosticstatutregles_diagnosticstatut_id_foreign');
            
            // Supprimer la colonne diagnosticstatut_id
            $table->dropColumn('diagnosticstatut_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticstatutregles', function (Blueprint $table) {
            // Restaurer la colonne diagnosticstatut_id
            $table->unsignedBigInteger('diagnosticstatut_id')->after('id');
            
            // Ajouter la clé étrangère
            $table->foreign('diagnosticstatut_id')->references('id')->on('diagnosticstatuts')->onDelete('cascade');
        });
    }
};
