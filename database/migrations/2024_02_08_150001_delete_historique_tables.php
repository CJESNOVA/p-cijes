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
        // Supprimer la table entrepriseprofil_historiques
        Schema::dropIfExists('entrepriseprofil_historiques');
        
        // Supprimer la table diagnosticstatuthistoriques
        Schema::dropIfExists('diagnosticstatuthistoriques');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table diagnosticstatuthistoriques si nécessaire
        Schema::create('diagnosticstatuthistoriques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnostic_id');
            $table->unsignedBigInteger('ancien_diagnosticstatut_id')->nullable();
            $table->unsignedBigInteger('nouveau_diagnosticstatut_id');
            $table->text('raison')->nullable();
            $table->integer('score_global')->nullable();
            $table->timestamps();
            
            $table->foreign('diagnostic_id')->references('id')->on('diagnostics')->onDelete('cascade');
            $table->foreign('ancien_diagnosticstatut_id')->references('id')->on('diagnosticstatuts')->onDelete('set null');
            $table->foreign('nouveau_diagnosticstatut_id')->references('id')->on('diagnosticstatuts')->onDelete('cascade');
        });
        
        // Recréer la table entrepriseprofil_historiques si nécessaire
        Schema::create('entrepriseprofil_historiques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entreprise_id');
            $table->unsignedBigInteger('ancien_profil_id');
            $table->unsignedBigInteger('nouveau_profil_id');
            $table->text('raison')->nullable();
            $table->decimal('score_global', 8, 2)->nullable();
            $table->integer('delai_mois')->default(0);
            $table->timestamps();
            
            $table->foreign('entreprise_id')->references('id')->on('entreprises')->onDelete('cascade');
        });
    }
};
