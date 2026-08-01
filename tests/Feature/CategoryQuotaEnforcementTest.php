<?php

namespace Tests\Feature;

use App\Filament\Pages\ZoneCamps;
use App\Models\Camp;
use App\Models\CampCategory;
use App\Models\Registration;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryQuotaEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_rejected_once_category_quota_is_reached(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Quota']);
        $camp = Camp::create(['name' => 'Test Camp Quota', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $campeur->update(['quota' => 1]);

        $component = Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id);

        // First registration fills the single available slot.
        $component->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'First Camper', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction();

        $this->assertSame(1, Registration::where('camp_category_id', $campeur->id)->count());

        // Second registration to the now-full category must be rejected.
        $component->mountTableAction('add_registration')
            ->setTableActionData(['nom' => 'Second Camper', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['camp_category_id']);

        $this->assertSame(1, Registration::where('camp_category_id', $campeur->id)->count());
        $this->assertNull(Registration::where('nom', 'Second Camper')->first());

        Registration::query()->delete();
        $camp->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_unlimited_category_without_quota_accepts_any_number_of_registrations(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone No Quota']);
        $camp = Camp::create(['name' => 'Test Camp No Quota', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        $this->assertNull($campeur->quota);

        $component = Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id);

        foreach (['A', 'B', 'C'] as $label) {
            $component->mountTableAction('add_registration')
                ->setTableActionData(['nom' => "Camper {$label}", 'camp_category_id' => $campeur->id])
                ->callMountedTableAction()
                ->assertHasNoTableActionErrors();
        }

        $this->assertSame(3, Registration::where('camp_category_id', $campeur->id)->count());

        Registration::query()->delete();
        $camp->delete();
        $zone->delete();
        $user->delete();
    }

    public function test_editing_a_registration_without_changing_its_full_category_is_still_allowed(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Quota Edit']);
        $camp = Camp::create(['name' => 'Test Camp Quota Edit', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();
        $campeur->update(['quota' => 1]);

        $registration = Registration::create([
            'camp_id' => $camp->id,
            'camp_category_id' => $campeur->id,
            'nom' => 'Existing Camper',
        ]);

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('edit', $registration)
            ->setTableActionData(['nom' => 'Existing Camper Renamed', 'camp_category_id' => $campeur->id])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertSame('Existing Camper Renamed', $registration->fresh()->nom);

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
