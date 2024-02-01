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
        Schema::create('gestion_cycles', function (Blueprint $table) {
            $table->id();
            $table->integer('nombre_de_cycle');
            $table->date('date_cycle');
            $table->enum('statut',['termine','a_venir']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestion_cycles');
    }
};

