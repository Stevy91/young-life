<?php

namespace Tests\Feature;

use App\Filament\Pages\ZoneCamps;
use App\Models\Camp;
use App\Models\Registration;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationReadOnlyRoleTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'gestionnaire_zone', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'agent_inscription', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'lecteur', 'guard_name' => 'web']);
    }

    public function test_lecteur_cannot_create_update_or_delete_a_registration(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Lecteur Policy']);
        $camp = Camp::create(['name' => 'Konbit Lecteur Policy', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $lecteur = User::factory()->create();
        $lecteur->assignRole('lecteur');
        $lecteur->zones()->attach($zone);

        $this->assertTrue(Gate::forUser($lecteur)->allows('view', $registration));
        $this->assertFalse(Gate::forUser($lecteur)->allows('create', Registration::class));
        $this->assertFalse(Gate::forUser($lecteur)->allows('update', $registration));
        $this->assertFalse(Gate::forUser($lecteur)->allows('delete', $registration));

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $lecteur->delete();
    }

    public function test_gestionnaire_zone_keeps_full_crud_in_their_zone(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Gestionnaire Policy']);
        $camp = Camp::create(['name' => 'Konbit Gestionnaire Policy', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $manager = User::factory()->create();
        $manager->assignRole('gestionnaire_zone');
        $manager->zones()->attach($zone);

        $this->assertTrue(Gate::forUser($manager)->allows('create', Registration::class));
        $this->assertTrue(Gate::forUser($manager)->allows('update', $registration));
        $this->assertTrue(Gate::forUser($manager)->allows('delete', $registration));

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $manager->delete();
    }

    public function test_zone_camps_page_hides_add_and_edit_delete_for_lecteur_but_keeps_print(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Lecteur UI']);
        $camp = Camp::create(['name' => 'Konbit Lecteur UI', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $lecteur = User::factory()->create();
        $lecteur->assignRole('lecteur');
        $lecteur->zones()->attach($zone);
        $this->actingAs($lecteur);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->assertTableActionHidden('add_registration')
            ->assertTableActionHidden('edit', $registration)
            ->assertTableActionHidden('delete', $registration)
            ->assertTableActionVisible('print_card', $registration)
            ->assertTableActionVisible('print_list');

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $lecteur->delete();
    }

    public function test_zone_camps_page_keeps_add_and_edit_delete_for_gestionnaire_zone(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Gestionnaire UI']);
        $camp = Camp::create(['name' => 'Konbit Gestionnaire UI', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $manager = User::factory()->create();
        $manager->assignRole('gestionnaire_zone');
        $manager->zones()->attach($zone);
        $this->actingAs($manager);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->assertTableActionVisible('add_registration')
            ->assertTableActionVisible('edit', $registration)
            ->assertTableActionVisible('delete', $registration);

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $manager->delete();
    }

    public function test_agent_inscription_can_only_create_not_update_or_delete(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Agent Policy']);
        $camp = Camp::create(['name' => 'Konbit Agent Policy', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $agent = User::factory()->create();
        $agent->assignRole('agent_inscription');
        $agent->zones()->attach($zone);

        $this->assertTrue(Gate::forUser($agent)->allows('view', $registration));
        $this->assertTrue(Gate::forUser($agent)->allows('create', Registration::class));
        $this->assertFalse(Gate::forUser($agent)->allows('update', $registration));
        $this->assertFalse(Gate::forUser($agent)->allows('delete', $registration));

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $agent->delete();
    }

    public function test_zone_camps_page_shows_add_but_hides_edit_delete_for_agent_inscription(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Agent UI']);
        $camp = Camp::create(['name' => 'Konbit Agent UI', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $agent = User::factory()->create();
        $agent->assignRole('agent_inscription');
        $agent->zones()->attach($zone);
        $this->actingAs($agent);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->assertTableActionVisible('add_registration')
            ->assertTableActionHidden('edit', $registration)
            ->assertTableActionHidden('delete', $registration)
            ->assertTableActionVisible('print_card', $registration);

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $agent->delete();
    }

    /**
     * Reproduces a real reported case: a test account had gestionnaire_zone,
     * lecteur, AND agent_inscription all assigned at once (roles toggled on
     * without unchecking previous ones). The highest-privilege role assigned
     * must win rather than "lecteur" silently vetoing everything.
     */
    public function test_gestionnaire_zone_wins_when_combined_with_lecteur_and_agent_inscription(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Combined Roles']);
        $camp = Camp::create(['name' => 'Konbit Combined Roles', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $user = User::factory()->create();
        $user->assignRole(['gestionnaire_zone', 'lecteur', 'agent_inscription']);
        $user->zones()->attach($zone);

        $this->assertTrue(Gate::forUser($user)->allows('create', Registration::class));
        $this->assertTrue(Gate::forUser($user)->allows('update', $registration));
        $this->assertTrue(Gate::forUser($user)->allows('delete', $registration));

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_agent_inscription_wins_when_combined_with_lecteur(): void
    {
        $this->seedRoles();

        $zone = Zone::create(['name' => 'Test Zone Agent Plus Lecteur']);
        $camp = Camp::create(['name' => 'Konbit Agent Plus Lecteur', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $registration = Registration::create(['camp_id' => $camp->id, 'camp_category_id' => $campeur->id, 'nom' => 'Existing Camper']);

        $user = User::factory()->create();
        $user->assignRole(['lecteur', 'agent_inscription']);
        $user->zones()->attach($zone);

        $this->assertTrue(Gate::forUser($user)->allows('create', Registration::class));
        $this->assertFalse(Gate::forUser($user)->allows('update', $registration));
        $this->assertFalse(Gate::forUser($user)->allows('delete', $registration));

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
