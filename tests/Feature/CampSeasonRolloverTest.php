<?php

namespace Tests\Feature;

use App\Enums\CampStatus;
use App\Filament\Pages\ZoneCamps;
use App\Filament\Resources\CampResource;
use App\Models\Camp;
use App\Models\Registration;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampSeasonRolloverTest extends TestCase
{
    use RefreshDatabase;

    public function test_archived_and_brouillon_camps_are_hidden_by_default_and_shown_via_toggle(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Rollover']);
        $active = Camp::create(['name' => 'Konbit Actif', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $archived = Camp::create(['name' => 'Konbit Vieux', 'zone_id' => $zone->id, 'statut' => 'archive']);
        $draft = Camp::create(['name' => 'Konbit Annule', 'zone_id' => $zone->id, 'statut' => 'brouillon']);

        $component = Livewire::test(ZoneCamps::class, ['zone' => $zone]);

        $names = fn () => $component->instance()->getCamps()->pluck('name')->all();

        $this->assertSame(['Konbit Actif'], $names());

        $component->call('toggleShowHidden');
        $this->assertEqualsCanonicalizing(['Konbit Actif', 'Konbit Vieux', 'Konbit Annule'], $names());

        $active->delete();
        $archived->delete();
        $draft->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_duplicating_a_camp_copies_roles_and_quotas_but_not_dates_or_registrations(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Duplicate']);
        $original = Camp::create([
            'name' => 'Konbit I',
            'zone_id' => $zone->id,
            'statut' => 'archive',
            'date_debut' => '2026-07-01',
            'date_fin' => '2026-07-04',
            'nb_nuits' => 3,
            'capacite' => 50,
        ]);

        $campeur = $original->categories()->where('name', 'Campeur')->first();
        $campeur->update(['quota' => 170]);
        $original->categories()->create(['name' => 'Bénévole', 'quota' => 10]);

        Registration::create([
            'camp_id' => $original->id,
            'camp_category_id' => $campeur->id,
            'nom' => 'Old Season Camper',
        ]);

        $clone = CampResource::duplicate($original->fresh());

        $this->assertNotSame($original->id, $clone->id);
        $this->assertSame('Konbit I', $clone->name);
        $this->assertSame($zone->id, $clone->zone_id);
        $this->assertSame(3, $clone->nb_nuits);
        $this->assertSame(50, $clone->capacite);
        $this->assertSame(CampStatus::Brouillon, $clone->statut);
        $this->assertNull($clone->date_debut);
        $this->assertNull($clone->date_fin);
        $this->assertSame(0, $clone->registrations()->count());

        $cloneCategories = $clone->categories()->pluck('quota', 'name');
        $this->assertSame(170, $cloneCategories['Campeur']);
        $this->assertSame(10, $cloneCategories['Bénévole']);

        // The original camp and its history must be untouched.
        $this->assertSame(1, $original->fresh()->registrations()->count());
        $this->assertSame('Old Season Camper', $original->fresh()->registrations()->first()->nom);

        $clone->registrations()->delete();
        $clone->delete();
        $original->registrations()->delete();
        $original->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_duplicate_action_is_reachable_from_camp_resource_table_for_super_admin(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Duplicate Action']);
        $camp = Camp::create(['name' => 'Konbit Action', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        Livewire::test(\App\Filament\Resources\CampResource\Pages\ListCamps::class)
            ->callTableAction('duplicate', $camp)
            ->assertHasNoTableActionErrors();

        $this->assertSame(2, Camp::where('name', 'Konbit Action')->count());

        Camp::where('name', 'Konbit Action')->get()->each(fn (Camp $c) => $c->delete());
        $zone->delete();
        $user->delete();
    }

    public function test_toggle_archive_flips_status_via_zone_camps_page(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Archive Toggle']);
        $camp = Camp::create(['name' => 'Konbit Toggle', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $component = Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id);

        $component->call('toggleArchiveCamp', $camp->id);
        $this->assertSame(CampStatus::Archive, $camp->fresh()->statut);

        // Archiving the selected camp removes it from the default (filtered)
        // view, so its detail panel disappears until "show archived" is on.
        $this->assertNull($component->instance()->getSelectedCamp());

        $component->call('toggleArchiveCamp', $camp->id);
        $this->assertSame(CampStatus::Ouvert, $camp->fresh()->statut);

        $camp->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_toggle_archive_action_is_reachable_from_camp_resource_table(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Archive Table Action']);
        $camp = Camp::create(['name' => 'Konbit Table Toggle', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        Livewire::test(\App\Filament\Resources\CampResource\Pages\ListCamps::class)
            ->callTableAction('toggle_archive', $camp)
            ->assertHasNoTableActionErrors();

        $this->assertSame(CampStatus::Archive, $camp->fresh()->statut);

        $camp->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_non_admin_cannot_toggle_archive(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $zone = Zone::create(['name' => 'Test Zone Archive Non Admin']);
        $camp = Camp::create(['name' => 'Konbit Non Admin', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $user = User::factory()->create();
        $user->zones()->attach($zone);
        $this->actingAs($user);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->call('toggleArchiveCamp', $camp->id)
            ->assertForbidden();

        $this->assertSame(CampStatus::Ouvert, $camp->fresh()->statut);

        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
