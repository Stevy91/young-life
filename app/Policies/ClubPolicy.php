<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    /**
     * Like zones and arrondissements, the master clubs list is org
     * structure: Super Admin-only. Zone managers still pick from their own
     * zone's clubs elsewhere (registration form) via a plain scoped query.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Club $club): bool
    {
        return $this->canAccessZone($user, $club);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Club $club): bool
    {
        return $this->canAccessZone($user, $club);
    }

    public function delete(User $user, Club $club): bool
    {
        return $this->canAccessZone($user, $club);
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }

    public function restore(User $user, Club $club): bool
    {
        return false;
    }

    public function forceDelete(User $user, Club $club): bool
    {
        return false;
    }

    private function canAccessZone(User $user, Club $club): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($club->arrondissement_id === null) {
            return true;
        }

        return $user->zones()->whereKey($club->arrondissement->zone_id)->exists();
    }
}
