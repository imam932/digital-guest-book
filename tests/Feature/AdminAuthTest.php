<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_accessible_to_guests(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Admin Sign In');
    }

    public function test_authenticated_admin_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/admin/login');

        $response->assertRedirect('/admin');
    }

    public function test_admin_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_is_redirected_to_admin_login_when_visiting_protected_admin_route(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
