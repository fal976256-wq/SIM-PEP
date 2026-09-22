<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\MasterStandarPagu;
use App\Models\UsulanPokir;
use App\Enums\JenisBantuan;
use App\Enums\UsulanStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::UTUSAN_DEWAN,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $component = \Livewire\Volt\Volt::test('pages.auth.login')
            ->set('form.email', 'test@test.com')
            ->set('form.password', 'password')
            ->set('acceptDisclaimer', true);

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/utusan/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_utusan_dewan_cannot_access_verifikator(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::UTUSAN_DEWAN,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/verifikator/dashboard');
        $response->assertStatus(403);
    }

    public function test_verifikator_cannot_access_utusan(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::VERIFIKATOR_DINAS,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/utusan/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_verifikator_routes(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN_PROV,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/verifikator/dashboard');
        $response->assertStatus(200);
    }

    public function test_inactive_user_cannot_access(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::UTUSAN_DEWAN,
            'is_active' => false,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/utusan/dashboard');
        $response->assertStatus(403);
    }
}
