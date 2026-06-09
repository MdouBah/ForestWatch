<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Rapport extends Model
{
    protected $fillable = ['analyse_id', 'user_id', 'titre', 'format', 'contenu', 'url_fichier'];

    /**
     * Ajouter download_url automatiquement dans toutes les réponses JSON.
     * Cela retourne l'URL publique du fichier PDF stocké dans storage/app/public/.
     * Aucune authentification JWT requise pour accéder à cette URL publique.
     */
    protected $appends = ['download_url'];

    public function getDownloadUrlAttribute(): ?string
    {
        if (! $this->url_fichier) {
            return null;
        }
        // Storage::disk('public')->url() retourne :
        // http://localhost:8000/storage/rapports/fichier.pdf
        return Storage::disk('public')->url($this->url_fichier);
    }

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
