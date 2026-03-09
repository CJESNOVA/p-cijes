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
        Schema::create('plantemplates', function (Blueprint $table) {
            $table->id();
            $table->text('objectif');
            $table->text('actionprioritaire');
            $table->integer('priorite')->default(1); // Priorité = délai en semaines
            $table->string('niveau', 1); // A, B, C, D
            $table->unsignedBigInteger('diagnosticmodule_id')->nullable();
            $table->unsignedBigInteger('diagnosticquestion_id')->nullable();
            $table->boolean('etat')->default(true);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('diagnosticmodule_id')->references('id')->on('diagnosticmodules')->onDelete('cascade');
            $table->foreign('diagnosticquestion_id')->references('id')->on('diagnosticquestions')->onDelete('cascade');
            
            // Indexes
            $table->index('diagnosticmodule_id');
            $table->index('diagnosticquestion_id');
            $table->index('niveau');
            $table->index('etat');
            
            // Contrainte : soit module_id, soit question_id doit être renseigné
            $table->check('(diagnosticmodule_id IS NOT NULL) OR (diagnosticquestion_id IS NOT NULL)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantemplates');
    }
};
