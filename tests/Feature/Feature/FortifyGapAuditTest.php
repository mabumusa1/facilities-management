<?php

namespace Tests\Feature\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FortifyGapAuditTest extends TestCase
{
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
}
