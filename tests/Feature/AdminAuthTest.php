<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_update_event_settings_with_uploaded_backgrounds(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/admin/settings', [
            'event_name' => 'Northwind Summit',
            'event_description' => 'A celebration of innovation and customer success.',
            'main_background' => UploadedFile::fake()->image('main-bg.jpg', 1200, 800)->size(120),
            'form_background' => UploadedFile::fake()->image('form-bg.png', 1200, 900)->size(120),
        ]);

        $response->assertRedirect('/admin/settings');

        $this->assertDatabaseHas('event_settings', [
            'event_name' => 'Northwind Summit',
            'event_description' => 'A celebration of innovation and customer success.',
        ]);

        $this->assertDatabaseCount('event_settings', 1);
        $this->assertTrue(Storage::disk('public')->exists('event-settings'));
    }
}
