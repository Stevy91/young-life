<?php

namespace App\Policies;

use App\Models\Camp;
use App\Models\User;

class CampPolicy
{
    /**
     * The cross-zone "Tous les camps" list is Super Admin-only setup/search
     * tooling. Zone-scoped users browse and manage their own camps through
     * the per-zone dashboard (ZoneCamps page) instead, which queries camps
     * directly and doesn't go through this policy.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Camp $camp): bool
    {
        return $this->canAccessZone($user, $camp);
    }

    /**
     * Setting up camps (create/delete, and the camp's own info fields) is
     * Super Admin-only. update() is Super Admin-only too: it gates the whole
     * Edit page, and zone-scoped users no longer need it — they register and
     * manage campers entirely through the ZoneCamps dashboard now.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Camp $camp): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Camp $camp): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Camp $camp): bool
    {
        return false;
    }

    public function forceDelete(User $user, Camp $camp): bool
    {
        return false;
    }

    /**
     * Super Admins can access every camp. Everyone else can access camps in
     * their assigned zone(s) only — every camp belongs to exactly one zone.
     */
    private function canAccessZone(User $user, Camp $camp): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->zones()->whereKey($camp->zone_id)->exists();
    }
}
