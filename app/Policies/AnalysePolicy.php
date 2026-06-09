<?php

namespace App\Policies;

use App\Models\Analyse;
use App\Models\User;

/**
 * AnalysePolicy
 *
 * Règles d'autorisation pour les analyses forestières :
 *  - viewAny  : agent ou admin
 *  - view     : agent ou admin
 *  - create   : agent ou admin
 *  - update   : auteur de l'analyse ou admin
 *  - delete   : auteur de l'analyse ou admin
 */
class AnalysePolicy
{
    /** Tout agent ou admin peut lister les analyses */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'user']);
    }

    /** Tout agent ou admin peut consulter une analyse */
    public function view(User $user, Analyse $analyse): bool
    {
        return in_array($user->role, ['admin', 'user']);
    }

    /** Un agent ou un admin peut créer une analyse */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'user']);
    }

    /** L'auteur de l'analyse ou un admin peut modifier */
    public function update(User $user, Analyse $analyse): bool
    {
        return $user->role === 'admin' || $analyse->user_id === $user->id;
    }

    /** L'auteur de l'analyse ou un admin peut supprimer */
    public function delete(User $user, Analyse $analyse): bool
    {
        return $user->role === 'admin' || $analyse->user_id === $user->id;
    }
}
