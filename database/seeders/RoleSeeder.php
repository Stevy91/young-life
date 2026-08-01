<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Role names only — what each one can actually see/do is governed by the
     * app's own Policies (zone membership, isSuperAdmin()), not by granular
     * Shield permissions. "super_admin" is special-cased by Filament Shield
     * itself (config/filament-shield.php) to bypass every policy check.
     */
    public function run(): void
    {
        foreach (['super_admin', 'gestionnaire_zone', 'agent_inscription', 'lecteur'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
