<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cotisation_tontines', function (Blueprint $table) {
            $table->id();
            $table->integer('montant_paiement');
            $table->date('date_paiement');
            $table->timestamps();

            $table->enum('statut',['gagnant','pas_gagnant'])->default('pas_gagnant');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisation_tontines');
    }
};
