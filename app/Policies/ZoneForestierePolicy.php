<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ZoneForestiere;

/**
 * ZoneForestierePolicy
 *
 * Règles d'autorisation pour les zones forestières :
 *  - viewAny  : tout utilisateur connecté (y compris visiteur) peut lister
 *  - view     : tout utilisateur connecté peut consulter
 *  - create   : agent (user) ou admin uniquement
 *  - update   : agent propriétaire ou admin
 *  - delete   : admin uniquement
 */
class ZoneForestierePolicy
{
    /** Tout utilisateur connecté peut voir la liste */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Tout utilisateur connecté peut consulter une zone */
    public function view(User $user, ZoneForestiere $zone): bool
    {
        return true;
    }

    /** Un agent ou un admin peut créer une zone */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'user']);
    }

    /** L'agent propriétaire ou un admin peut modifier */
    public function update(User $user, ZoneForestiere $zone): bool
    {
        return $user->role === 'admin' || $zone->user_id === $user->id;
    }

    /** Seul l'admin peut supprimer */
    public function delete(User $user, ZoneForestiere $zone): bool
    {
        return $user->role === 'admin';
    }
}
