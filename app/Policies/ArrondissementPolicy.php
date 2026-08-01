<?php

namespace App\Policies;

use App\Models\Arrondissement;
use App\Models\User;

class ArrondissementPolicy
{
    /**
     * Like zones, the arrondissement list (and which zone each one belongs
     * to) is org structure: Super Admin-only. Zone managers still pick from
     * their own zone's arrondissements elsewhere via a plain scoped query.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Arrondissement $arrondissement): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Arrondissement $arrondissement): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Arrondissement $arrondissement): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Arrondissement $arrondissement): bool
    {
        return false;
    }

    public function forceDelete(User $user, Arrondissement $arrondissement): bool
    {
        return false;
    }
}
