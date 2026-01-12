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
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entreprise_id')->nullable()->default(0);
            $table->unsignedBigInteger('cotisationtype_id')->nullable()->default(0);
            $table->decimal('montant', 10, 2)->default(0);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('montant_restant', 10, 2)->default(0);
            $table->string('devise', 10)->default('XOF');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->date('date_echeance')->nullable();
            $table->date('date_paiement')->nullable();
            $table->string('statut', 20)->default('en_attente');
            $table->boolean('est_a_jour')->default(0);
            $table->integer('nombre_rappels')->default(0);
            $table->string('reference_paiement')->nullable();
            $table->string('mode_paiement', 20)->nullable();
            $table->text('commentaires')->nullable();
            $table->boolean('etat')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};
