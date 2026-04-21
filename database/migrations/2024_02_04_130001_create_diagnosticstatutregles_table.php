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
        Schema::create('diagnosticstatutregles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnosticstatut_id');
            $table->integer('score_total_min')->nullable();
            $table->integer('score_total_max')->nullable();
            $table->integer('min_blocs_score')->nullable();
            $table->integer('min_score_bloc')->nullable();
            $table->integer('bloc_juridique_min')->nullable();
            $table->integer('bloc_finance_min')->nullable();
            $table->integer('aucun_bloc_inf')->nullable();
            $table->integer('duree_min_mois')->default(0);
            $table->timestamps();
            
            $table->foreign('diagnosticstatut_id')->references('id')->on('diagnosticstatuts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosticstatutregles');
    }
};
