<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspeceArbre extends Model
{
    protected $table = 'especes_arbres';

    protected $fillable = ['nom_commun', 'nom_scientifique', 'famille', 'description', 'image_url', 'statut'];

    public function zones()
    {
        return $this->belongsToMany(ZoneForestiere::class, 'zone_espece', 'espece_arbre_id', 'zone_forestiere_id');
    }
}
