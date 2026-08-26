<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserOptionalEmailTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsSuperAdmin(): Role
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $lecteur = Role::firstOrCreate(['name' => 'lecteur', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        return $lecteur;
    }

    public function test_a_user_can_be_created_without_an_email(): void
    {
        $role = $this->actingAsSuperAdmin();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'No Email User',
                'username' => 'noemailuser',
                'email' => '',
                'password' => 'password',
                'roles' => $role->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('username', 'noemailuser')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email);
    }

    public function test_two_users_can_both_be_created_without_an_email(): void
    {
        $role = $this->actingAsSuperAdmin();

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'First', 'username' => 'firstnoemail', 'email' => '', 'password' => 'password', 'roles' => $role->id])
            ->call('create')
            ->assertHasNoFormErrors();

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Second', 'username' => 'secondnoemail', 'email' => '', 'password' => 'password', 'roles' => $role->id])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(2, User::whereIn('username', ['firstnoemail', 'secondnoemail'])->whereNull('email')->count());
    }
}
