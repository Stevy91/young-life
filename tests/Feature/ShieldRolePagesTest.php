<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShieldRolePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_create_and_edit_pages_load_without_crashing(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        // Shield's role pages instantiate every panel Page class (including
        // ZoneCamps) outside its normal mount() lifecycle to build
        // permission labels, which previously crashed on the uninitialized
        // typed $zone property.
        $this->get('/admin/shield/roles/create')->assertOk();
        $this->get("/admin/shield/roles/{$role->id}/edit")->assertOk();
    }
}
