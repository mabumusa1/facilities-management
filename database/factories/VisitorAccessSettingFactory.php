<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\VisitorAccessSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorAccessSetting>
 */
class VisitorAccessSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id' => Community::factory(),
            'require_id_verification' => false,
            'allow_walk_in' => true,
            'qr_expiry_minutes' => 1440,
            'max_uses_per_invitation' => 1,
        ];
    }

    /**
     * Require ID verification on entry.
     */
    public function requiresIdVerification(): static
    {
        return $this->state(['require_id_verification' => true]);
    }

    /**
     * Disable walk-in visitors (pre-registration required).
     */
    public function noWalkIn(): static
    {
        return $this->state(['allow_walk_in' => false]);
    }
}
