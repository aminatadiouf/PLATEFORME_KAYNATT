<?php

use App\Models\ParticipationTontine;
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
        Schema::create('gestion_cycles', function (Blueprint $table) {
            $table->id();

            $table->integer('nombre_de_cycle');
            $table->date('date_cycle');
            $table->enum('statut',['termine','a_venir'])->default('a_venir');
            $table->enum('statutCotisation',['cotise','Noncotise'])->default('Noncotise');
            $table->enum('statutTirage',['gagnant','pasgagnant'])->default('pasgagnant');

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

