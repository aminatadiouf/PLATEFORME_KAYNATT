<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GestionCycle extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'tontine_id',
        'nombre_de_cycle',
        'date_cycle',
        'statut'
    ];

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }


//     public function CotisationTontines()
// {
//     return $this->hasMany(CotisationTontine::class );
// }

public function payments()
{
    return $this->hasMany(Payment::class );
}

}
