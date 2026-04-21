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
        Schema::create('propositions', function (Blueprint $table) {
            $table->id();

            // Relations principales
            $table->unsignedBigInteger('membre_id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('prestation_id');
            $table->foreign('membre_id')->references('id')->on('membres')->onDelete('cascade');
            $table->foreign('expert_id')->references('id')->on('experts')->onDelete('cascade');
            $table->foreign('prestation_id')->references('id')->on('prestations')->onDelete('cascade');

            // Liens contextuels (optionnels)
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('accompagnement_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->foreign('accompagnement_id')->references('id')->on('accompagnements')->onDelete('set null');

            // Contenu de la proposition
            $table->text('message')->nullable();
            $table->decimal('prix_propose', 12, 2)->nullable();
            $table->integer('duree_prevue')->nullable();

            // Statut
            $table->unsignedBigInteger('propositionstatut_id')->nullable();
            $table->foreign('propositionstatut_id')->references('id')->on('propositionstatuts')->onDelete('set null');
            $table->dateTime('date_proposition')->nullable();
            $table->dateTime('date_expiration')->nullable();

            // Métadonnées
            $table->boolean('spotlight')->default(0);
            $table->boolean('etat')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propositions');
    }
};
