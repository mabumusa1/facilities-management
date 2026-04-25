<?php

namespace Tests\Feature\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FortifyGapAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_fortify_features_are_enabled(): void
    {
        $features = config('fortify.features');

        $expected = [
            'registration',
            'reset-passwords',
            'email-verification',
            'update-profile-information',
            'update-passwords',
            'two-factor-authentication',
        ];

        foreach ($expected as $feature) {
            $this->assertContains($feature, $features, "Feature '{$feature}' is not enabled");
        }
    }

    public function test_fortify_update_profile_information_route_is_registered(): void
    {
        $this->assertTrue(
            Route::getRoutes()->hasNamedRoute('user-profile-information.update'),
            'Route user-profile-information.update is not registered'
        );
    }

    public function test_fortify_update_password_route_is_registered(): void
    {
        $this->assertTrue(
            Route::getRoutes()->hasNamedRoute('user-password.update'),
            'Route user-password.update is not registered'
        );
    }

    public function test_fortify_two_factor_routes_are_registered(): void
    {
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('two-factor.login'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('two-factor.enable'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('two-factor.disable'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('two-factor.qr-code'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('two-factor.recovery-codes'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('two-factor.secret-key'));
    }

    public function test_fortify_password_confirmation_routes_are_registered(): void
    {
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('password.confirm'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('password.confirmation'));
    }

    public function test_password_reset_routes_are_registered(): void
    {
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('password.request'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('password.email'));
        $this->assertTrue(Route::getRoutes()->hasNamedRoute('password.update'));
    }

    public function test_user_can_update_profile_name(): void
    {
        $user = User::factory()->create(['name' => 'Original Name', 'email' => 'original@example.com']);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Updated Name',
            'email' => 'original@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('original@example.com', $user->email);
    }

    public function test_user_can_update_profile_email(): void
    {
        $user = User::factory()->create(['email' => 'original@example.com']);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
    }

    public function test_profile_update_fails_when_name_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => '',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('name', 'updateProfileInformation');
    }

    public function test_profile_update_fails_when_email_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Test User',
            'email' => '',
        ]);

        $response->assertSessionHasErrors('email', 'updateProfileInformation');
    }

    public function test_profile_update_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);
        $user = User::factory()->create(['email' => 'current@example.com']);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
        ]);

        $response->assertSessionHasErrors('email', 'updateProfileInformation');
    }

    public function test_profile_update_fails_with_invalid_email_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Test User',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email', 'updateProfileInformation');
    }

    public function test_user_can_update_locale_and_phone_number(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'locale' => 'ar',
            'phone_number' => '1234567890',
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame('ar', $user->locale);
        $this->assertSame('1234567890', $user->phone_number);
    }

    public function test_profile_update_validates_locale_max_length(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'locale' => str_repeat('x', 6),
        ]);

        $response->assertSessionHasErrors('locale', 'updateProfileInformation');
    }

    public function test_profile_update_validates_phone_number_max_length(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => str_repeat('0', 21),
        ]);

        $response->assertSessionHasErrors('phone_number', 'updateProfileInformation');
    }

    public function test_email_change_clears_verified_at_for_must_verify_email_user(): void
    {
        $user = new class extends User implements MustVerifyEmail
        {
            use \Illuminate\Auth\MustVerifyEmail;

            protected $table = 'users';
        };

        $user->forceFill([
            'name' => 'Original Name',
            'email' => 'old@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ])->save();

        $this->assertTrue($user instanceof MustVerifyEmail);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $user->refresh();
        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password', 'updatePassword');
    }

    public function test_password_update_fails_when_current_password_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/password', [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password', 'updatePassword');
    }

    public function test_password_update_fails_when_new_password_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('password', 'updatePassword');
    }

    public function test_password_update_fails_with_mismatched_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password', 'updatePassword');
    }

    public function test_password_update_fails_with_weak_new_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password',
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ]);

        $response->assertSessionHasErrors('password', 'updatePassword');
    }

    public function test_unauthenticated_user_cannot_update_profile(): void
    {
        $response = $this->put('/user/profile-information', [
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect();
    }

    public function test_unauthenticated_user_cannot_update_password(): void
    {
        $response = $this->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect();
    }
}
