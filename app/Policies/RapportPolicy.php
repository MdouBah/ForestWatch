<?php

namespace App\Policies;

use App\Models\Rapport;
use App\Models\User;

/**
 * RapportPolicy
 *
 * Règles d'autorisation pour les rapports PDF :
 *  - viewAny  : agent ou admin
 *  - view     : auteur ou admin
 *  - create   : agent ou admin
 *  - delete   : auteur ou admin
 */
class RapportPolicy
{
    /** Tout agent ou admin peut voir la liste des rapports */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'user']);
    }

    /** L'auteur ou un admin peut consulter un rapport */
    public function view(User $user, Rapport $rapport): bool
    {
        return $user->role === 'admin' || $rapport->user_id === $user->id;
    }

    /** Un agent ou un admin peut générer un rapport */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'user']);
    }

    /** L'auteur ou un admin peut supprimer un rapport */
    public function delete(User $user, Rapport $rapport): bool
    {
        return $user->role === 'admin' || $rapport->user_id === $user->id;
    }
}
