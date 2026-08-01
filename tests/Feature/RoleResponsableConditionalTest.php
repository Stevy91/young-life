<?php

namespace Tests\Feature;

use App\Filament\Resources\RegistrationResource\Pages\CreateRegistration;
use App\Models\Camp;
use App\Models\CampCategory;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleResponsableConditionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_responsable_field_toggles_with_category(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Diag']);
        $camp = Camp::create(['name' => 'Test Camp Diag', 'zone_id' => $zone->id, 'statut' => 'ouvert']);

        $responsable = CampCategory::where('camp_id', $camp->id)->where('name', 'Responsable')->first();
        $campeur = CampCategory::where('camp_id', $camp->id)->where('name', 'Campeur')->first();

        $component = Livewire::test(CreateRegistration::class)
            ->fillForm(['camp_id' => $camp->id]);

        $component->assertDontSee('Type de responsable');

        $component->fillForm(['camp_category_id' => $campeur->id]);
        $component->assertDontSee('Type de responsable');

        $component->fillForm(['camp_category_id' => $responsable->id]);
        $component->assertSee('Type de responsable');

        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
