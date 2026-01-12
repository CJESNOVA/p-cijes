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
        Schema::create('reductiontypes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->unsignedBigInteger('entrepriseprofil_id')->nullable();
            $table->unsignedBigInteger('offretype_id')->nullable();
            $table->decimal('pourcentage', 5, 2)->nullable()->default(0);
            $table->decimal('montant', 10, 2)->nullable()->default(0);
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('etat')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['entrepriseprofil_id', 'offretype_id']);
            $table->index(['date_debut', 'date_fin']);
            $table->index('etat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reductiontypes');
    }
};
