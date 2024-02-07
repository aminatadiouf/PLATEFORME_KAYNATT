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
        Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('participation_Tontine_id')->after('id');
         
                $table->foreign('participation_Tontine_id')->references('id')->on('participation_tontines');
            });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['participation_Tontine_id']);
            $table->dropColumn('participation_Tontine_id'); 
        });
    }
};
