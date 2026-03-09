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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->text('objectif');
            $table->text('actionprioritaire');
            $table->date('dateplan');
            $table->unsignedBigInteger('accompagnement_id');
            $table->unsignedBigInteger('diagnostic_id')->nullable();
            $table->unsignedBigInteger('plantemplate_id')->nullable();
            $table->boolean('spotlight')->default(false);
            $table->boolean('etat')->default(true);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('accompagnement_id')->references('id')->on('accompagnements')->onDelete('cascade');
            $table->foreign('diagnostic_id')->references('id')->on('diagnostics')->onDelete('cascade');
            $table->foreign('plantemplate_id')->references('id')->on('plantemplates')->onDelete('cascade');
            
            // Indexes
            $table->index('accompagnement_id');
            $table->index('diagnostic_id');
            $table->index('plantemplate_id');
            $table->index('etat');
            $table->index('dateplan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
