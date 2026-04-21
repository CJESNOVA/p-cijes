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
        Schema::create('moduleressources', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2);
            $table->string('reference', 100)->unique();
            $table->text('description')->nullable();
            $table->string('module_type', 50); // diagnostics, formations, etc.
            $table->integer('module_id'); // ID du module spécifique
            $table->foreignId('membre_id')->constrained()->onDelete('cascade');
            $table->foreignId('entreprise_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ressourcecompte_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('paiementstatut_id')->default(1)->constrained();
            $table->boolean('spotlight')->default(0);
            $table->boolean('etat')->default(1);
            $table->timestamps();
            
            // Index
            $table->index(['module_type', 'module_id']);
            $table->index('membre_id');
            $table->index('entreprise_id');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moduleressources');
    }
};
