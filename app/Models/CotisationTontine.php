<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotisationTontine extends Model
{
    use HasFactory;

    protected $fillable = [
        'participation_Tontine_id',
        'montant_paiement',
        'date_paiement',
        'gestion_cycle_id',
        'statut',
        'statutCotisation',
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
