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
            $table->unsignedBigInteger('diagnosticmodule_id');
            $table->string('niveau');
            $table->text('objectif')->nullable();
            $table->longText('actionprioritaire')->nullable();
            $table->integer('priorite')->default(1);
            $table->boolean('spotlight')->default(0);
            $table->boolean('etat')->default(1);
            $table->timestamps();

            // Foreign keys
            $table->foreign('diagnosticmodule_id')
                  ->references('id')
                  ->on('diagnosticmodules')
                  ->onDelete('cascade');

            // Indexes
            $table->index('diagnosticmodule_id');
            $table->index('niveau');
            $table->index(['etat', 'spotlight']);
            $table->index('priorite');
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
