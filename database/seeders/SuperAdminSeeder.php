<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Dev-only placeholder account so the panel is reachable right after
     * install. Change this password from the Users screen before real use.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@younglife.test'],
            ['name' => 'Super Admin', 'username' => 'admin', 'password' => 'password'],
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }
    }
}
