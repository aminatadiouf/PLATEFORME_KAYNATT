<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Tontine extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'user_id',
        'libelle',
        'description',
        'montant',
        // 'nombre_participant',
        'regles',
        'date_de_debut',
        'periode',
        'etat', 
        'statutTontine' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participationTontines()
{
    return $this->hasMany(ParticipationTontine::class);
}


public function gestion_cycles()
{
    return $this->hasMany(GestionCycle::class);
}

}
