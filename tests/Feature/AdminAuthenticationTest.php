<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin user can be created via seeder
     */
    public function test_admin_user_can_be_created(): void
    {
        User::query()->delete();

        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
            'is_all_communities' => true,
            'is_all_buildings' => true,
        ]);

        $this->assertNotNull($admin->id);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasManagerRole(ManagerRole::Admin));
        $this->assertTrue($admin->is_all_communities);
        $this->assertTrue($admin->is_all_buildings);
    }

    /**
     * Test that admin user can login with email and password
     */
    public function test_admin_can_login_with_credentials(): void
    {
        $admin = User::factory()
            ->admin()
            ->create([
                'email' => 'admin@login.test',
                'password' => 'password123',
            ]);

        $response = $this->post('/login', [
            'email' => 'admin@login.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test that admin user cannot login with wrong password
     */
    public function test_admin_cannot_login_with_wrong_password(): void
    {
        User::factory()
            ->admin()
            ->create([
                'email' => 'admin@secure.test',
                'password' => 'correctpassword',
            ]);

        $response = $this->post('/login', [
            'email' => 'admin@secure.test',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /**
     * Test that admin can access protected dashboard
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::factory()
            ->admin()
            ->create();

        $response = $this->actingAs($admin)
            ->get('/dashboard');

        $response->assertSuccessful();
    }

    /**
     * Test creating admin user via factory
     */
    public function test_admin_can_be_created_via_factory(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertEquals(ContactType::Admin, $admin->contact_type);
        $this->assertEquals(ManagerRole::Admin, $admin->manager_role);
    }
}
