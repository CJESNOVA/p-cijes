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
        Schema::create('diagnosticmodulescores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnostic_id');
            $table->unsignedBigInteger('diagnosticmodule_id');
            $table->integer('score_total')->nullable();
            $table->integer('score_max')->nullable();
            $table->decimal('score_pourcentage', 5, 2)->nullable();
            $table->string('niveau')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('diagnostic_id')
                  ->references('id')
                  ->on('diagnostics')
                  ->onDelete('cascade');

            $table->foreign('diagnosticmodule_id')
                  ->references('id')
                  ->on('diagnosticmodules')
                  ->onDelete('cascade');

            // Indexes
            $table->index('diagnostic_id');
            $table->index('diagnosticmodule_id');
            $table->index('score_pourcentage');
            $table->index('niveau');
            $table->unique(['diagnostic_id', 'diagnosticmodule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosticmodulescores');
    }
};
