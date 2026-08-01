<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSingleRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_a_new_role_replaces_the_previous_one_instead_of_adding_to_it(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $gestionnaireZone = Role::firstOrCreate(['name' => 'gestionnaire_zone', 'guard_name' => 'web']);
        $lecteur = Role::firstOrCreate(['name' => 'lecteur', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        // Reproduces the reported case: a user already has gestionnaire_zone
        // assigned from earlier testing.
        $subject = User::factory()->create();
        $subject->assignRole('gestionnaire_zone');

        Livewire::test(EditUser::class, ['record' => $subject->getRouteKey()])
            ->fillForm(['roles' => $lecteur->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $subject->refresh();

        $this->assertEqualsCanonicalizing(['lecteur'], $subject->roles->pluck('name')->all());
        $this->assertTrue($subject->isReadOnly());
    }
}
