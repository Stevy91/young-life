<?php

namespace Tests\Feature;

use App\Filament\Pages\Content\GalleryPageContent;
use App\Filament\Pages\Content\HomePageContent;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PageContentAdminTest extends TestCase
{
    use RefreshDatabase;

    private function seedMinimalHomeContent(): void
    {
        PageContent::updateOrCreate(['page' => 'home'], ['data' => [
            'hero_title' => 'Titre original',
            'hero_slides' => [['image' => 'page-content/slider/slide2.jpeg']],
            'mission_title' => 'Notre Mission',
            'mission_text' => 'Texte mission',
            'vision_title' => 'Notre Vision',
            'vision_text' => 'Texte vision',
            'value_title' => 'Notre Valeur',
            'value_text' => 'Texte valeur',
            'about_checklist' => [],
            'camp_highlights' => [],
        ]]);
    }

    public function test_super_admin_can_access_all_four_content_pages(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $this->get('/admin/home-page-content')->assertOk();
        $this->get('/admin/leve-fonds-page-content')->assertOk();
        $this->get('/admin/about-page-content')->assertOk();
        $this->get('/admin/gallery-page-content')->assertOk();
    }

    public function test_non_admin_is_blocked_from_content_pages(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/admin/home-page-content')->assertForbidden();
        $this->get('/admin/leve-fonds-page-content')->assertForbidden();
        $this->get('/admin/about-page-content')->assertForbidden();
        $this->get('/admin/gallery-page-content')->assertForbidden();
    }

    public function test_saving_home_page_content_updates_the_database(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $this->seedMinimalHomeContent();

        Livewire::test(HomePageContent::class)
            ->fillForm(['hero_title' => 'Nouveau titre modifié'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Nouveau titre modifié', PageContent::forPage('home')->fresh()->data['hero_title']);

        // Closes the loop end-to-end: the public homepage must actually
        // read from PageContent, not the old hardcoded array.
        $this->get('/')->assertSee('Nouveau titre modifié');
    }

    /**
     * Existing (already-uploaded) images are plain string paths in
     * PageContent's JSON — this proves a save round-trips them through
     * Filament's Repeater + FileUpload without corrupting or dropping any.
     */
    public function test_gallery_photos_survive_an_unrelated_save(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        PageContent::updateOrCreate(['page' => 'galerie'], ['data' => [
            'hero_title' => 'Galerie',
            'photos' => [
                ['image' => 'page-content/portfolio/g1.jpg'],
                ['image' => 'page-content/portfolio/g2.jpg'],
                ['image' => 'page-content/portfolio/g3.jpg'],
            ],
        ]]);

        Livewire::test(GalleryPageContent::class)
            ->fillForm(['hero_title' => 'Galerie photo'])
            ->call('save')
            ->assertHasNoFormErrors();

        $fresh = PageContent::forPage('galerie')->fresh()->data;

        $this->assertSame('Galerie photo', $fresh['hero_title']);
        $this->assertSame(
            ['page-content/portfolio/g1.jpg', 'page-content/portfolio/g2.jpg', 'page-content/portfolio/g3.jpg'],
            array_column($fresh['photos'], 'image'),
        );
    }
}
