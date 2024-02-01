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
        Schema::table('participation_tontines', function (Blueprint $table) {
            $table->unsignedBigInteger('tontine_id')->after('id');
 
            $table->foreign('tontine_id')->references('id')->on('tontines')
             ->constrained()
             ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participation_tontines', function (Blueprint $table) {
            $table->dropForeign(['tontine_id']);
            $table->dropColumn('tontine_id'); 
        });
    }
};
