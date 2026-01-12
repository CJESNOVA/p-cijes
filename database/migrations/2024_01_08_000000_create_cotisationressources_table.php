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
        Schema::create('cotisationressources', function (Blueprint $table) {
            $table->id();
            $table->string('montant')->nullable();
            $table->text('reference')->nullable();
            $table->unsignedBigInteger('accompagnement_id')->nullable()->default(0);
            $table->unsignedBigInteger('ressourcecompte_id')->nullable()->default(0);
            $table->unsignedBigInteger('cotisation_id')->nullable()->default(0);
            $table->unsignedBigInteger('paiementstatut_id')->nullable()->default(0);
            $table->unsignedBigInteger('membre_id')->nullable()->default(0);
            $table->unsignedBigInteger('entreprise_id')->nullable()->default(0);
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
        Schema::dropIfExists('cotisationressources');
    }
};
