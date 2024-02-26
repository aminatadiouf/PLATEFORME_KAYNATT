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
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->string('description');
            $table->integer('montant');
             $table->integer('nombre_participant');
            $table->string('regles');
            $table->date('date_de_debut');
            $table->enum('periode',['hebdomaire','mensuel','quotidien','annuel']);
            $table->enum('etat',['en_attente','en_cours','termine'])->default('en_attente');
            $table->enum('statutTontine',['en_attente','accepte','refuse'])->default('en_attente');
            $table->integer('nombreParticipantsAccepte')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tontines');
    }
};
