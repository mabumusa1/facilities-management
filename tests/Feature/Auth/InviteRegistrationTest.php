<?php

namespace Tests\Feature\Auth;

use App\Models\InviteCode;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InviteRegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_register_page_shows_with_invite_code(): void
    {
        $this->withoutVite();

        $response = $this->get('/register?code=TEST-CODE');

        $response->assertOk();
    }

    public function test_register_page_without_code_shows_normal_form(): void
    {
        $this->withoutVite();

        $response = $this->get('/register');

        $response->assertOk();
    }

    public function test_invite_registration_with_valid_code(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        $inviteCode = InviteCode::create([
            'code' => 'VALID-CODE-123',
            'tenant_id' => $tenant->id,
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->postJson('/register', [
            'code' => 'VALID-CODE-123',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'name' => 'Test Resident',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', ['name' => 'Test Resident']);
        $this->assertDatabaseHas('invite_codes', [
            'id' => $inviteCode->id,
            'used_by' => User::where('name', 'Test Resident')->first()?->id,
        ]);
    }

    public function test_invite_registration_with_invalid_code_fails(): void
    {
        $response = $this->postJson('/register', [
            'code' => 'NONEXISTENT',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    public function test_invite_registration_with_expired_code_fails(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        InviteCode::create([
            'code' => 'EXPIRED-CODE',
            'tenant_id' => $tenant->id,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/register', [
            'code' => 'EXPIRED-CODE',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422);
    }

    public function test_invite_registration_with_used_code_fails(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $user = User::factory()->create();

        InviteCode::create([
            'code' => 'USED-CODE',
            'tenant_id' => $tenant->id,
            'used_by' => $user->id,
            'used_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->postJson('/register', [
            'code' => 'USED-CODE',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422);
    }
}
