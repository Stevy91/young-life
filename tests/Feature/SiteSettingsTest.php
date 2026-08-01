<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_site_title_shows_in_the_header_when_unset(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $response = $this->get('/admin');

        $response->assertOk();

        // The brandLogo is also set, and Filament's logo component only
        // renders brandName visibly when there's no logo (it becomes the
        // <img alt="..."> otherwise, invisible on screen). assertSeeText
        // strips tags/attributes first, so this only passes if the title is
        // real rendered text, not just hidden inside an alt attribute.
        $response->assertSeeText(SiteSettings::SITE_TITLE_DEFAULT);
    }

    public function test_super_admin_can_change_the_site_title(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        Livewire::test(SiteSettings::class)
            ->fillForm(['site_title' => 'Camps YoungLife 2027'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Camps YoungLife 2027', Setting::get(SiteSettings::SITE_TITLE_KEY));

        $this->get('/admin')->assertSee('Camps YoungLife 2027');
    }

    public function test_non_admin_cannot_access_site_settings(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(SiteSettings::class)->assertForbidden();
    }
}
