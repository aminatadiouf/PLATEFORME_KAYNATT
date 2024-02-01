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
        Schema::table('cotisation_tontines', function (Blueprint $table) {
            
            $table->unsignedBigInteger('gestion_cycle_id')->after('id');

            $table->foreign('gestion_cycle_id')->references('id')->on('cotisation_tontines')
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
        Schema::table('cotisation_tontines', function (Blueprint $table) {


            
            Schema::table('cotisation_tontines', function (Blueprint $table) {
                $table->dropForeign(['gestion_cycle_id']);
                $table->dropColumn('gestion_cycle_id'); 
            });

        });
    }
};
