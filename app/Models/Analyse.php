<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    protected $fillable = [
        'zone_forestiere_id', 'user_id', 'type_analyse',
        'resultat', 'superficie_concernee', 'taux_deforestation',
        'observations', 'date_analyse',
    ];

    protected $casts = ['date_analyse' => 'datetime'];

    public function zone()
    {
        return $this->belongsTo(ZoneForestiere::class, 'zone_forestiere_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rapports()
    {
        return $this->hasMany(Rapport::class);
    }
}
