<?php

namespace Tests\Feature;

use App\Filament\Pages\ZoneCamps;
use App\Models\Camp;
use App\Models\Registration;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DateNaissanceDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_selecting_day_month_year_saves_the_combined_date(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $zone = Zone::create(['name' => 'Zone Date Naissance']);
        $camp = Camp::create(['name' => 'Camp Date Naissance', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData([
                'nom' => 'Date Test Camper',
                'camp_category_id' => $campeur->id,
                'date_naissance_day' => 15,
                'date_naissance_month' => 8,
                'date_naissance_year' => 2010,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $registration = Registration::where('nom', 'Date Test Camper')->first();
        $this->assertNotNull($registration);
        $this->assertSame('2010-08-15', $registration->date_naissance->toDateString());
    }

    public function test_invalid_day_month_combination_leaves_date_naissance_blank(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $zone = Zone::create(['name' => 'Zone Date Naissance Invalid']);
        $camp = Camp::create(['name' => 'Camp Date Naissance Invalid', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData([
                'nom' => 'Invalid Date Camper',
                'camp_category_id' => $campeur->id,
                'date_naissance_day' => 31,
                'date_naissance_month' => 2,
                'date_naissance_year' => 2010,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $registration = Registration::where('nom', 'Invalid Date Camper')->first();
        $this->assertNotNull($registration);
        $this->assertNull($registration->date_naissance);
    }

    public function test_editing_a_registration_prefills_day_month_year_from_the_stored_date(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $zone = Zone::create(['name' => 'Zone Date Naissance Edit']);
        $camp = Camp::create(['name' => 'Camp Date Naissance Edit', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $registration = Registration::create([
            'camp_id' => $camp->id,
            'camp_category_id' => $campeur->id,
            'nom' => 'Existing Dated Camper',
            'date_naissance' => '2005-03-22',
        ]);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('edit', $registration)
            ->assertTableActionDataSet([
                'date_naissance_day' => 22,
                'date_naissance_month' => 3,
                'date_naissance_year' => 2005,
            ]);
    }
}
