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
        Schema::table('diagnosticmodulescores', function (Blueprint $table) {
            $table->unsignedBigInteger('diagnosticblocstatut_id')->nullable();
            
            $table->foreign('diagnosticblocstatut_id')
                  ->references('id')
                  ->on('diagnosticblocstatuts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticmodulescores', function (Blueprint $table) {
            $table->dropForeign(['diagnosticblocstatut_id']);
            $table->dropColumn('diagnosticblocstatut_id');
        });
    }
};
