<?php

namespace Tests\Feature\Auth;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
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
        $this->seed(SubscriptionPlanSeeder::class);
    }

    /**
     * @return array<string, string>
     */
    private function validRegistrationPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone_number' => '+966500000000',
            'tenant_name' => 'Test Account',
            'terms' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), $this->validRegistrationPayload());

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_creates_a_tenant(): void
    {
        $this->post(route('register.store'), $this->validRegistrationPayload());

        $this->assertDatabaseCount('tenants', 1);

        $tenant = Tenant::first();
        $this->assertEquals('Test Account', $tenant->name);
        $this->assertNull($tenant->domain);
    }

    public function test_registration_stores_user_phone_number(): void
    {
        $this->post(route('register.store'), $this->validRegistrationPayload());

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone_number' => '+966500000000',
        ]);
    }

    public function test_registration_creates_account_membership(): void
    {
        $this->post(route('register.store'), $this->validRegistrationPayload());

        $this->assertDatabaseCount('account_memberships', 1);

        $membership = AccountMembership::first();
        $this->assertEquals(RolesEnum::ACCOUNT_ADMINS->value, $membership->role);
    }

    public function test_registration_assigns_account_admin_role(): void
    {
        $this->post(route('register.store'), $this->validRegistrationPayload());

        $user = User::first();
        $this->assertTrue($user->hasRole(RolesEnum::ACCOUNT_ADMINS));
    }

    public function test_registration_creates_default_subscription_for_tenant(): void
    {
        $this->post(route('register.store'), $this->validRegistrationPayload());

        $tenant = Tenant::firstOrFail();

        $this->assertDatabaseHas(config('laravel-subscriptions.tables.subscriptions'), [
            'subscriber_type' => Tenant::class,
            'subscriber_id' => $tenant->id,
            'slug' => 'main',
        ]);
    }

    public function test_registration_sets_tenant_in_session(): void
    {
        $response = $this->post(route('register.store'), $this->validRegistrationPayload());

        $tenant = Tenant::first();
        $response->assertSessionHas('tenant_id', $tenant->id);
    }

    public function test_registration_validates_required_fields(): void
    {
        $response = $this->post(route('register.store'), []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'tenant_name', 'email', 'phone_number', 'terms', 'password']);
        $this->assertGuest();
    }

    public function test_registration_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register.store'), $this->validRegistrationPayload([
            'email' => 'taken@example.com',
        ]));

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
