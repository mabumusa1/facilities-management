<?php

namespace Tests\Feature\Auth;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
        $this->seed(RolesSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_creates_a_tenant(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount('tenants', 1);

        $tenant = Tenant::first();
        $this->assertEquals("Test User's Account", $tenant->name);
    }

    public function test_registration_creates_account_membership(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount('account_memberships', 1);

        $membership = AccountMembership::first();
        $this->assertEquals(RolesEnum::ACCOUNT_ADMINS->value, $membership->role);
    }

    public function test_registration_assigns_account_admin_role(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::first();
        $this->assertTrue($user->hasRole(RolesEnum::ACCOUNT_ADMINS));
    }

    public function test_registration_sets_tenant_in_session(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $tenant = Tenant::first();
        $response->assertSessionHas('tenant_id', $tenant->id);
    }

    public function test_registration_validates_required_fields(): void
    {
        $response = $this->post(route('register.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
