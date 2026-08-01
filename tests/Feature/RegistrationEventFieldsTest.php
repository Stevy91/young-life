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

class RegistrationEventFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_fields_save_on_registration_via_zone_camps_modal(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        $zone = Zone::create(['name' => 'Test Zone Event Fields']);
        $camp = Camp::create(['name' => 'Test Camp Event Fields', 'zone_id' => $zone->id, 'statut' => 'ouvert']);
        $campeur = $camp->categories()->where('name', 'Campeur')->first();

        Livewire::test(ZoneCamps::class, ['zone' => $zone])
            ->call('selectCamp', $camp->id)
            ->mountTableAction('add_registration')
            ->setTableActionData([
                'nom' => 'Event Fields Camper',
                'camp_category_id' => $campeur->id,
                'campus' => 'Campus Test',
                'adresse_campus' => '123 Rue Test',
                'camp_de_jour' => '1',
                'type_camp' => 'Formation',
            ])
            ->callMountedTableAction();

        $registration = Registration::where('nom', 'Event Fields Camper')->first();

        $this->assertNotNull($registration);
        $this->assertSame('Campus Test', $registration->campus);
        $this->assertSame('123 Rue Test', $registration->adresse_campus);
        $this->assertTrue($registration->camp_de_jour);
        $this->assertSame('Formation', $registration->type_camp);

        $registration->delete();
        $camp->delete();
        $zone->delete();
        $user->delete();
    }
}
