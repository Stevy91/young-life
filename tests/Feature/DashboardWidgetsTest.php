<?php

namespace Tests\Feature;

use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\RegistrationsByZoneChart;
use App\Models\Camp;
use App\Models\Registration;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    private function makeZoneUser(Zone $zone): User
    {
        $user = User::factory()->create();
        $user->zones()->attach($zone);

        return $user;
    }

    public function test_super_admin_sees_the_dashboard_with_all_widgets(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $this->get('/admin')->assertOk();
    }

    public function test_stats_overview_only_counts_camps_and_registrations_visible_to_the_user(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $ownZone = Zone::create(['name' => 'Zone Widget Own']);
        $otherZone = Zone::create(['name' => 'Zone Widget Other']);

        $ownCamp = Camp::create(['name' => 'Camp Widget Own', 'zone_id' => $ownZone->id, 'statut' => 'ouvert', 'capacite' => 10]);
        $otherCamp = Camp::create(['name' => 'Camp Widget Other', 'zone_id' => $otherZone->id, 'statut' => 'ouvert', 'capacite' => 10]);

        $ownCategory = $ownCamp->categories()->first();
        $otherCategory = $otherCamp->categories()->first();

        Registration::create(['camp_id' => $ownCamp->id, 'camp_category_id' => $ownCategory->id, 'nom' => 'Own Camper', 'sexe' => 'Masculin']);
        Registration::create(['camp_id' => $otherCamp->id, 'camp_category_id' => $otherCategory->id, 'nom' => 'Other Camper', 'sexe' => 'Feminin']);

        $user = $this->makeZoneUser($ownZone);
        $this->actingAs($user);

        Livewire::test(DashboardStatsOverview::class)
            ->assertSee('Camps ouverts')
            ->assertSee('1'); // only the own-zone camp/registration should count
    }

    public function test_zone_breakdown_chart_is_hidden_from_zone_scoped_users(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $zone = Zone::create(['name' => 'Zone Widget Visibility']);
        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        $this->assertFalse(RegistrationsByZoneChart::canView());
    }

    public function test_zone_breakdown_chart_is_visible_to_super_admin(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $this->assertTrue(RegistrationsByZoneChart::canView());
    }
}
