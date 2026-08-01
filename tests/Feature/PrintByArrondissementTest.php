<?php

namespace Tests\Feature;

use App\Filament\Pages\ZoneCamps;
use App\Models\Arrondissement;
use App\Models\Camp;
use App\Models\Registration;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PrintByArrondissementTest extends TestCase
{
    use RefreshDatabase;

    private function makeCampWithArrondissements(): array
    {
        $zone = Zone::create(['name' => 'Test Zone Print']);
        $camp = Camp::create(['name' => 'Konbit Print', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $arrPap = Arrondissement::create(['zone_id' => $zone->id, 'name' => 'ARR PORT AU PRINCE']);
        $arrGonave = Arrondissement::create(['zone_id' => $zone->id, 'name' => 'ARR DE LA GONAVE']);

        Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Pap Camper', 'arrondissement_id' => $arrPap->id]);
        Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Gonave Camper', 'arrondissement_id' => $arrGonave->id]);

        return [$zone, $camp, $arrPap, $arrGonave];
    }

    public function test_guest_cannot_download_the_print_route(): void
    {
        [$zone, $camp] = $this->makeCampWithArrondissements();

        $this->get(route('camps.print-list', ['camp' => $camp]))->assertForbidden();
    }

    public function test_super_admin_can_print_full_list_and_filtered_by_arrondissement(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        [$zone, $camp, $arrPap] = $this->makeCampWithArrondissements();

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $full = $this->get(route('camps.print-list', ['camp' => $camp]));
        $full->assertOk();
        $full->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('liste-konbit-print.pdf', $full->headers->get('Content-Disposition'));

        $filtered = $this->get(route('camps.print-list', ['camp' => $camp, 'arrondissement' => $arrPap->id]));
        $filtered->assertOk();
        $this->assertStringContainsString('liste-konbit-print-arr-port-au-prince.pdf', $filtered->headers->get('Content-Disposition'));
    }

    public function test_zone_scoped_user_can_print_their_own_zone_but_not_another_zone(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        [$zone, $camp] = $this->makeCampWithArrondissements();

        $otherZone = Zone::create(['name' => 'Other Zone Print']);
        $otherCamp = Camp::create(['name' => 'Other Camp Print', 'zone_id' => $otherZone->id, 'statut' => 'ouvert']);

        $user = User::factory()->create();
        $user->zones()->attach($zone);
        $this->actingAs($user);

        $this->get(route('camps.print-list', ['camp' => $camp]))->assertOk();
        $this->get(route('camps.print-list', ['camp' => $otherCamp]))->assertForbidden();

        $otherCamp->delete();
        $otherZone->delete();
    }

    public function test_print_popup_lists_only_arrondissements_with_registrants_in_this_camp(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        [$zone, $camp, $arrPap, $arrGonave] = $this->makeCampWithArrondissements();

        // An arrondissement in the same zone but with no registrants in this
        // camp must not appear as a print option.
        Arrondissement::create(['zone_id' => $zone->id, 'name' => 'ARR SANS INSCRIT']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('print_list')
            ->assertSee('ARR PORT AU PRINCE')
            ->assertSee('ARR DE LA GONAVE')
            ->assertDontSee('ARR SANS INSCRIT');
    }
}
