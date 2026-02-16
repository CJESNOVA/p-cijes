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
        Schema::table('diagnosticorientations', function (Blueprint $table) {
            // Supprimer la clé étrangère avec le bon nom
            $table->dropForeign('diagnosticorientations_diagnosticstatut_id_foreign');
            
            // Supprimer la colonne ancien_diagnosticstatut_id
            $table->dropColumn('ancien_diagnosticstatut_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticorientations', function (Blueprint $table) {
            // Restaurer la colonne ancien_diagnosticstatut_id
            $table->unsignedBigInteger('ancien_diagnosticstatut_id')->nullable()->after('diagnosticblocstatut_id');
            
            // Ajouter la clé étrangère
            $table->foreign('ancien_diagnosticstatut_id')->references('id')->on('diagnosticstatuts')->onDelete('cascade');
        });
    }
};
