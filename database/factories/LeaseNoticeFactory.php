<?php

namespace Database\Factories;

use App\Enums\LeaseNoticeType;
use App\Models\Lease;
use App\Models\LeaseNotice;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseNotice>
 */
class LeaseNoticeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lease_id' => Lease::factory(),
            'tenant_id' => Resident::factory(),
            'sent_by' => User::factory(),
            'type' => fake()->randomElement(LeaseNoticeType::cases())->value,
            'subject_en' => fake()->sentence(),
            'body_en' => fake()->paragraph(),
            'subject_ar' => fake()->sentence(),
            'body_ar' => fake()->paragraph(),
            'sent_at' => now(),
        ];
    }
}
