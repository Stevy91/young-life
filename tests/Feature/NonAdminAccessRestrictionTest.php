<?php

namespace Tests\Feature;

use App\Filament\Pages\ZoneCamps;
use App\Models\Camp;
use App\Models\Club;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NonAdminAccessRestrictionTest extends TestCase
{
    use RefreshDatabase;

    private function makeZoneUser(Zone $zone): User
    {
        $user = User::factory()->create();
        $user->zones()->attach($zone);

        return $user;
    }

    public function test_non_admin_is_blocked_from_admin_only_resources_but_keeps_zone_camps_access(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $zone = Zone::create(['name' => 'Test Zone Restriction']);
        $camp = Camp::create(['name' => 'Test Camp Restriction', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        Club::create(['name' => 'Test Club Restriction']);

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        // Circled-as-hidden nav destinations: all must now 403 for a
        // zone-scoped, non-Super Admin user.
        $this->get('/admin/camps')->assertForbidden();
        $this->get("/admin/camps/{$camp->id}/edit")->assertForbidden();
        $this->get('/admin/registrations')->assertForbidden();
        $this->get('/admin/clubs')->assertForbidden();
        $this->get('/admin/zones')->assertForbidden();
        $this->get('/admin/arrondissements')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();

        // The actual workflow zone-scoped users rely on must keep working.
        $this->get("/admin/zones/{$zone->id}/camps")->assertOk();

        $camp->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_super_admin_keeps_full_access(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $zone = Zone::create(['name' => 'Test Zone Admin Access']);
        $camp = Camp::create(['name' => 'Test Camp Admin Access', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $this->get('/admin/camps')->assertOk();
        $this->get("/admin/camps/{$camp->id}/edit")->assertOk();
        $this->get('/admin/registrations')->assertOk();
        $this->get('/admin/clubs')->assertOk();
        $this->get('/admin/zones')->assertOk();
        $this->get('/admin/arrondissements')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get("/admin/zones/{$zone->id}/camps")->assertOk();

        $camp->delete();
        $zone->delete();
        $admin->delete();
    }

    public function test_zone_camps_page_hides_edit_camp_link_from_non_admin(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $zone = Zone::create(['name' => 'Test Zone Link Visibility']);
        $camp = Camp::create(['name' => 'Test Camp Link Visibility', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        \Livewire\Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->assertDontSee('Modifier les informations du camp');

        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
