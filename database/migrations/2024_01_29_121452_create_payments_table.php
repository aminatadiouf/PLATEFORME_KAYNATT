<?php

use App\Models\GestionCycle;
use App\Models\ParticipationTontine;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->string('token')->unique()->nullable();

           // $table->foreignIdFor(ParticipationTontine::class)->constrained()->onDelete('Cascade')->onUpdate('Cascade');
            $table->foreignIdFor(GestionCycle::class)->constrained()->onDelete('Cascade')->onUpdate('Cascade');

            $table->enum('statutCotisation',['cotise','Noncotise'])->default('Noncotise');
            $table->integer('montant_a_gagner')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
