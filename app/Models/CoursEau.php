<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursEau extends Model
{
    protected $table = 'cours_eaux';

    protected $fillable = ['nom', 'type', 'longueur', 'debit', 'coordonnees'];

    protected $casts = ['coordonnees' => 'array'];

    public function zones()
    {
        return $this->belongsToMany(ZoneForestiere::class, 'zone_cours_eau', 'cours_eau_id', 'zone_forestiere_id');
    }
}
