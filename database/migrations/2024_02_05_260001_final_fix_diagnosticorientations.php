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
            // Renommer l'ancienne colonne pour garder la compatibilité
            $table->renameColumn('diagnosticstatut_id', 'ancien_diagnosticstatut_id');
            
            // Ajouter la nouvelle colonne correcte
            $table->unsignedBigInteger('diagnosticblocstatut_id')->nullable()->after('diagnosticmodule_id');
            
            // Ajouter la clé étrangère correcte
            $table->foreign('diagnosticblocstatut_id', 'diag_orient_bloc_fk')->references('id')->on('diagnosticblocstatuts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticorientations', function (Blueprint $table) {
            // Supprimer la clé étrangère
            $table->dropForeign('diag_orient_bloc_fk');
            
            // Supprimer la nouvelle colonne
            $table->dropColumn('diagnosticblocstatut_id');
            
            // Restaurer l'ancienne colonne
            $table->renameColumn('ancien_diagnosticstatut_id', 'diagnosticstatut_id');
        });
    }
};
