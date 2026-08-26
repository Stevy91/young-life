<?php

namespace Tests\Feature;

use App\Models\Camp;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampRolesPerCampTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_camp_gets_its_own_default_roles_independently(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $zone = Zone::create(['name' => 'Zone Roles Test']);
        $campA = Camp::create(['name' => 'Konbit 1', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campB = Camp::create(['name' => 'Camp Priere', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $this->assertSame(4, $campA->categories()->count());
        $this->assertSame(4, $campB->categories()->count());

        // Remove 2 of camp B's roles - must not affect camp A at all.
        $campB->categories()->whereIn('name', ['Campeur(jenn Manman)', 'Conseiller'])->delete();

        $this->assertSame(4, $campA->fresh()->categories()->count());
        $this->assertSame(2, $campB->fresh()->categories()->count());
        $this->assertEqualsCanonicalizing(
            ['Campeur', 'Responsable'],
            $campB->fresh()->categories()->pluck('name')->all(),
        );
    }

    public function test_deleting_a_role_from_the_ui_only_affects_that_camp(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $zone = Zone::create(['name' => 'Zone Roles UI Test']);
        $campA = Camp::create(['name' => 'Konbit 1 UI', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campB = Camp::create(['name' => 'Camp Priere UI', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $categoryToRemove = $campB->categories()->where('name', 'Conseiller')->first();

        Livewire::test(\App\Filament\Resources\CampResource\RelationManagers\CategoriesRelationManager::class, [
            'ownerRecord' => $campB,
            'pageClass' => \App\Filament\Resources\CampResource\Pages\EditCamp::class,
        ])
            ->callTableAction('delete', $categoryToRemove);

        $this->assertSame(3, $campB->fresh()->categories()->count());
        $this->assertSame(4, $campA->fresh()->categories()->count());
    }
}
