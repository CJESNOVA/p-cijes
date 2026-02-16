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
        Schema::table('diagnosticmodules', function (Blueprint $table) {
            $table->unsignedBigInteger('entrepriseprofil_id')->nullable()->after('diagnosticmoduletype_id');
            
            // Ajout de la clé étrangère
            $table->foreign('entrepriseprofil_id')
                  ->references('id')
                  ->on('entrepriseprofils')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticmodules', function (Blueprint $table) {
            $table->dropForeign(['entrepriseprofil_id']);
            $table->dropColumn('entrepriseprofil_id');
        });
    }
};
