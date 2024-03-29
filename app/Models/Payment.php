<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\SwaggerExclude
 */

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'participation_Tontine_id',
        'gestion_cycle_id',
        'montant_paiement',
        'date_paiement',
        'token',
        'statut'
    ];

    protected $table = 'payments';

    public function participationTontine()
    {
        return $this->belongsTo(ParticipationTontine::class );
    }

    public function gestionCycle()
    {
        return $this->belongsTo(GestionCycle::class );
    }

}