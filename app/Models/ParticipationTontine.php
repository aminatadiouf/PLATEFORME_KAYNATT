<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ParticipationTontine extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'user_id',
        'tontine_id',
        'statutParticipation',
        'date',
];

public function users()
{
    return $this->hasMany(User::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

// public function cotisationTontines()
// {
//     return $this->hasMany(CotisationTontine::class);
// }

public function payments()
{
    return $this->hasMany(Payment::class );
}


public function tontine()
{
    return $this->belongsTo(Tontine::class);
}

}
