<?php

namespace App\Policies;

use App\Models\CampCategory;
use App\Models\User;

class CampCategoryPolicy
{
    /**
     * Categories & quotas are part of a camp's setup — Super Admin-only,
     * same as the camp itself (see CampPolicy). Zone-scoped users can still
     * view them (they need to see quotas while registering campers).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CampCategory $campCategory): bool
    {
        return $this->canAccessCamp($user, $campCategory);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, CampCategory $campCategory): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, CampCategory $campCategory): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, CampCategory $campCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, CampCategory $campCategory): bool
    {
        return false;
    }

    private function canAccessCamp(User $user, CampCategory $campCategory): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->zones()->whereKey($campCategory->camp->zone_id)->exists();
    }
}
