<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Zone;

class ZonePolicy
{
    /**
     * The master zones/arrondissements list is system configuration:
     * Super Admin-only. Zone managers still pick from it (their own zone)
     * when creating camps/clubs — that uses a plain scoped query, not this policy.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Zone $zone): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Zone $zone): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Zone $zone): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Zone $zone): bool
    {
        return false;
    }

    public function forceDelete(User $user, Zone $zone): bool
    {
        return false;
    }
}
