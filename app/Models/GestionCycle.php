<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GestionCycle extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        // 'participation_Tontine_id',
        'tontine_id',
        'nombre_de_cycle',
        'date_cycle',
       
    ];

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }


    public function CotisationTontines()
{
    return $this->hasMany(CotisationTontine::class );
}

public function payments()
{
    return $this->hasMany(Payment::class );
}

public function participationTontines()
{
    return $this->hasMany(ParticipationTontine::class , 'participation_Tontine_id' );
}
}
