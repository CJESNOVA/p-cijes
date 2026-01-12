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
        Schema::table('cotisationtypes', function (Blueprint $table) {
            $table->unsignedBigInteger('entrepriseprofil_id')->nullable()->default(0)->after('montant');
            $table->integer('nombre_jours')->nullable()->default(0)->after('entrepriseprofil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotisationtypes', function (Blueprint $table) {
            $table->dropColumn(['entrepriseprofil_id', 'nombre_jours']);
        });
    }
};
