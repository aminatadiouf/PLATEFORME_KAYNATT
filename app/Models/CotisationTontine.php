<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CotisationTontine extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'participation_Tontine_id',
        'montant_paiement',
        'date_paiement',
        'gestion_cycle_id',
        'statut',
        'statutCotisation',
        'montant_a_gagner'
];

//ça conccerne les paiements
public function participationTontine()
{
    return $this->belongsTo(ParticipationTontine::class );
}

public function gestionCycle()
{
    return $this->belongsTo(GestionCycle::class );
}


}
