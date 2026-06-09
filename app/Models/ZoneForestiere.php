<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneForestiere extends Model
{
    protected $table = 'zones_forestieres';

    protected $fillable = ['nom', 'superficie', 'latitude', 'longitude', 'region', 'etat', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function especes()
    {
        return $this->belongsToMany(EspeceArbre::class, 'zone_espece', 'zone_forestiere_id', 'espece_arbre_id');
    }

    public function coursEaux()
    {
        return $this->belongsToMany(CoursEau::class, 'zone_cours_eau', 'zone_forestiere_id', 'cours_eau_id');
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'zone_forestiere_id');
    }

    public function latestAnalyse()
    {
        return $this->hasOne(Analyse::class, 'zone_forestiere_id')->latestOfMany('date_analyse');
    }
}
