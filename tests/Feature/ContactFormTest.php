<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_loads(): void
    {
        $this->get('/contact')->assertOk()->assertSee('Contactez-nous');
    }

    public function test_valid_submission_is_stored_and_redirects_with_success_flash(): void
    {
        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Test Utilisateur',
            'email' => 'test@example.com',
            'subject' => 'Question test',
            'message' => 'Ceci est un message de test.',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('contact_sent', true);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Test Utilisateur',
            'email' => 'test@example.com',
            'subject' => 'Question test',
            'message' => 'Ceci est un message de test.',
        ]);

        // The flashed success banner should render on the next request.
        $this->followingRedirects()
            ->get('/contact')
            ->assertSee('Merci');
    }

    public function test_missing_required_fields_are_rejected(): void
    {
        $response = $this->from('/contact')->post('/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertSame(0, ContactMessage::count());
    }
}
