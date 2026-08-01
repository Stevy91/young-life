<?php

namespace Tests\Feature;

use App\Filament\Pages\ZoneCamps;
use App\Models\Camp;
use App\Models\CampCategory;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ZoneCampsModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_responsable_field_toggles_inside_zone_camps_modal(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Diag']);
        $camp = Camp::create(['name' => 'Test Camp Diag', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $responsable = CampCategory::where('camp_id', $camp->id)->where('name', 'Responsable')->first();
        $campeur = CampCategory::where('camp_id', $camp->id)->where('name', 'Campeur')->first();

        $component = Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration');

        // "Type de responsable" is ambiguous on this page: it's both the
        // form field's label AND the toggleable table column's label (they
        // describe the same underlying data). The column-toggle checkbox is
        // always present, so count occurrences instead of presence alone —
        // 1 = only the column toggle; 2 = the form field is also showing.
        $countLabel = fn () => substr_count($component->html(), 'Type de responsable');

        $this->assertSame(1, $countLabel(), 'Expected only the column-toggle label with no category selected');

        $component->setTableActionData(['camp_category_id' => $campeur->id]);
        $this->assertSame(1, $countLabel(), 'Expected only the column-toggle label with Campeur selected');

        $component->setTableActionData(['camp_category_id' => $responsable->id]);
        $this->assertSame(2, $countLabel(), 'Expected the form field to also appear with Responsable selected');

        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
