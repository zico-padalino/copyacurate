<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_page_is_shown(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Kabar Banten')
            ->assertSee('Masuk');
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@kabarbanten.test',
            'password' => 'password',
            'role' => 'owner',
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'budi@kabarbanten.test',
            'password' => 'password',
        ]);

        $this->from(route('login'))->post(route('login'), [
            'email' => 'budi@kabarbanten.test',
            'password' => 'salah',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
