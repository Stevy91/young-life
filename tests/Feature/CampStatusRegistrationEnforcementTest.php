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

class CampStatusRegistrationEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'gestionnaire_zone', 'guard_name' => 'web']);
    }

    private function makeZoneUser(Zone $zone): User
    {
        $user = User::factory()->create();
        $user->assignRole('gestionnaire_zone');
        $user->zones()->attach($zone);

        return $user;
    }

    public function test_new_registration_is_rejected_when_camp_is_ferme(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Camp Ferme']);
        $camp = Camp::create(['name' => 'Test Camp Ferme', 'zone_id' => $zone->id, 'statut' => 'ferme']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'Late Camper', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['camp_category_id']);

        $this->assertNull(Registration::where('nom', 'Late Camper')->first());
    }

    /**
     * Reproduces the exact reported bug: every role option is correctly
     * disabled on a closed camp, but that alone doesn't stop the form from
     * being submitted with camp_category_id left empty — the closure rule
     * never ran at all for a null value, so nothing blocked the submission.
     */
    public function test_new_registration_with_no_category_selected_is_still_rejected_on_a_closed_camp(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone No Category Ferme']);
        $camp = Camp::create(['name' => 'Test Camp No Category Ferme', 'zone_id' => $zone->id, 'statut' => 'ferme']);

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'No Role Late Camper'])
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['camp_category_id']);

        $this->assertNull(Registration::where('nom', 'No Role Late Camper')->first());
    }

    public function test_new_registration_is_rejected_when_camp_is_brouillon(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Camp Brouillon']);
        $camp = Camp::create(['name' => 'Test Camp Brouillon', 'zone_id' => $zone->id, 'statut' => 'brouillon']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'Too Early Camper', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['camp_category_id']);

        $this->assertNull(Registration::where('nom', 'Too Early Camper')->first());
    }

    public function test_new_registration_is_accepted_when_camp_is_ouvert(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Camp Ouvert Status']);
        $camp = Camp::create(['name' => 'Test Camp Ouvert Status', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'On Time Camper', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertNotNull(Registration::where('nom', 'On Time Camper')->first());
    }

    public function test_super_admin_can_still_add_a_registration_to_a_closed_camp(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Super Admin Override']);
        $camp = Camp::create(['name' => 'Test Camp Super Admin Override', 'zone_id' => $zone->id, 'statut' => 'ferme']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'Admin Added Camper', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertNotNull(Registration::where('nom', 'Admin Added Camper')->first());
    }

    public function test_deleting_an_existing_registration_is_blocked_once_the_camp_is_closed(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Delete After Close']);
        $camp = Camp::create(['name' => 'Test Camp Delete After Close', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $registration = Registration::create([
            'camp_id' => $camp->id,
            'camp_category_id' => $campeur->id,
            'nom' => 'Camper Before Close',
        ]);

        $camp->update(['statut' => 'ferme']);

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->callTableAction('delete', $registration);

        $this->assertNotNull(Registration::find($registration->id));
    }

    public function test_editing_an_existing_registration_is_blocked_once_the_camp_is_closed(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Edit After Close']);
        $camp = Camp::create(['name' => 'Test Camp Edit After Close', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $registration = Registration::create([
            'camp_id' => $camp->id,
            'camp_category_id' => $campeur->id,
            'nom' => 'Camper Before Close',
        ]);

        $camp->update(['statut' => 'ferme']);

        $user = $this->makeZoneUser($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('edit', $registration)
            ->setTableActionData(['nom' => 'Camper Before Close Renamed'])
            ->callMountedTableAction();

        $this->assertSame('Camper Before Close', $registration->fresh()->nom);
    }

    public function test_super_admin_can_still_edit_and_delete_on_a_closed_camp(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Admin Edit Delete Override']);
        $camp = Camp::create(['name' => 'Test Camp Admin Edit Delete Override', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $registration = Registration::create([
            'camp_id' => $camp->id,
            'camp_category_id' => $campeur->id,
            'nom' => 'Camper Before Close',
        ]);

        $camp->update(['statut' => 'ferme']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('edit', $registration)
            ->setTableActionData(['nom' => 'Camper Renamed By Admin', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction();

        $this->assertSame('Camper Renamed By Admin', $registration->fresh()->nom);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->callTableAction('delete', $registration);

        $this->assertNull(Registration::find($registration->id));
    }
}
