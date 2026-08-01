<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * The cross-camp "Toutes les inscriptions" search list is Super
     * Admin-only. Zone-scoped users register and manage campers per-camp
     * through the ZoneCamps dashboard instead, which queries registrations
     * directly and doesn't go through this policy.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Registration $registration): bool
    {
        return $this->canAccessCamp($user, $registration);
    }

    /**
     * Creating a registration always happens in the context of a camp
     * (relation manager, or the camp_id picker on the standalone resource);
     * CampPolicy already gates which camps a user can see/pick. "Lecteur"
     * is the one role excluded — it's view/print-only.
     */
    public function create(User $user): bool
    {
        return ! $user->isReadOnly();
    }

    /**
     * "Agent d'inscription" is create-only (see User::canManageRegistrations)
     * — it can add campers but not touch existing ones.
     */
    public function update(User $user, Registration $registration): bool
    {
        return $user->canManageRegistrations() && $this->canAccessCamp($user, $registration);
    }

    public function delete(User $user, Registration $registration): bool
    {
        return $user->canManageRegistrations() && $this->canAccessCamp($user, $registration);
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageRegistrations();
    }

    public function restore(User $user, Registration $registration): bool
    {
        return false;
    }

    public function forceDelete(User $user, Registration $registration): bool
    {
        return false;
    }

    private function canAccessCamp(User $user, Registration $registration): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->zones()->whereKey($registration->camp->zone_id)->exists();
    }
}
