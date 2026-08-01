<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UsernameLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_username_and_password(): void
    {
        $user = User::factory()->create([
            'username' => 'campstaff',
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'username' => 'campstaff',
                'password' => 'correct-password',
            ])
            ->call('authenticate');

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    public function test_login_form_has_no_email_field(): void
    {
        Livewire::test(Login::class)
            ->assertFormFieldExists('username')
            ->assertFormFieldDoesNotExist('email');
    }

    public function test_email_is_rejected_as_a_login_credential(): void
    {
        User::factory()->create([
            'username' => 'campstaff',
            'email' => 'campstaff@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Typing the email address into the username field must not
        // authenticate — login is username-only now.
        Livewire::test(Login::class)
            ->fillForm([
                'username' => 'campstaff@example.com',
                'password' => 'correct-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors();

        $this->assertFalse(Auth::check());
    }

    public function test_wrong_password_fails_with_error_on_username_field(): void
    {
        User::factory()->create([
            'username' => 'campstaff',
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'username' => 'campstaff',
                'password' => 'wrong-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['username']);

        $this->assertFalse(Auth::check());
    }
}
