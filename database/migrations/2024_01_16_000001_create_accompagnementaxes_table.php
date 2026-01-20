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
        Schema::create('accompagnementaxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diagnosticmodule_id');
            $table->string('titre');
            $table->longText('description')->nullable();
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
            $table->index(['etat', 'spotlight']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accompagnementaxes');
    }
};
