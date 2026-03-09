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
        Schema::create('accompagnements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entreprise_id');
            $table->unsignedBigInteger('membre_id');
            $table->unsignedBigInteger('diagnostic_id')->nullable();
            $table->unsignedBigInteger('accompagnementniveau_id')->default(1);
            $table->date('dateaccompagnement');
            $table->unsignedBigInteger('accompagnementstatut_id')->default(1);
            $table->boolean('spotlight')->default(false);
            $table->boolean('etat')->default(true);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('entreprise_id')->references('id')->on('entreprises')->onDelete('cascade');
            $table->foreign('membre_id')->references('id')->on('membres')->onDelete('cascade');
            $table->foreign('diagnostic_id')->references('id')->on('diagnostics')->onDelete('cascade');
            $table->foreign('accompagnementniveau_id')->references('id')->on('accompagnementniveaux')->onDelete('cascade');
            $table->foreign('accompagnementstatut_id')->references('id')->on('accompagnementstatuts')->onDelete('cascade');
            
            // Indexes
            $table->index('entreprise_id');
            $table->index('membre_id');
            $table->index('diagnostic_id');
            $table->index('accompagnementniveau_id');
            $table->index('accompagnementstatut_id');
            $table->index('etat');
            $table->index('dateaccompagnement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accompagnements');
    }
};
