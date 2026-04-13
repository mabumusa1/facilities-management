<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Lease;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lease>
 */
class LeaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+3 months');
        $years = $this->faker->numberBetween(1, 3);
        $months = $this->faker->numberBetween(0, 11);
        $endDate = (clone $startDate)->modify("+{$years} years +{$months} months");

        $rentalAmount = $this->faker->randomFloat(2, 10000, 200000);

        return [
            'tenant_id' => Contact::factory()->tenant(),
            'status_id' => Status::where('domain', 'lease')->inRandomOrder()->first()->id ?? 1,
            'created_by_id' => Contact::factory()->admin(),
            'deal_owner_id' => null,
            'community_id' => Community::factory(),
            'building_id' => Building::factory(),
            'lease_unit_type_id' => $this->faker->numberBetween(1, 3),
            'rental_contract_type_id' => $this->faker->numberBetween(13, 15),
            'payment_schedule_id' => $this->faker->numberBetween(7, 10),
            'parent_lease_id' => null,
            'contract_number' => $this->faker->unique()->regexify('[0-9]{10}RL'),
            'tenant_type' => $this->faker->randomElement(['individual', 'corporate']),
            'rental_type' => $this->faker->randomElement(['summary', 'detailed']),
            'rental_total_amount' => $rentalAmount,
            'security_deposit_amount' => $rentalAmount * 0.1,
            'security_deposit_due_date' => $startDate,
            'legal_representative' => $this->faker->optional()->name(),
            'fit_out_status' => $this->faker->optional()->randomElement(['not_started', 'in_progress', 'completed']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'handover_date' => $startDate,
            'actual_end_at' => null,
            'free_period' => $this->faker->numberBetween(0, 30),
            'number_of_years' => $years,
            'number_of_months' => $months,
            'number_of_days' => 0,
            'lease_escalations_type' => $this->faker->randomElement(['fixed', 'percentage']),
            'lease_escalations' => null,
            'additional_fees_lease' => null,
            'terms_conditions' => $this->faker->optional()->paragraph(),
            'is_terms' => $this->faker->boolean(30),
            'is_sub_lease' => false,
            'is_renew' => false,
            'is_move_out' => false,
            'is_old' => false,
            'pdf_url' => null,
        ];
    }

    /**
     * Indicate that the lease is currently active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subMonths(3),
            'end_date' => now()->addMonths(9),
            'is_move_out' => false,
        ]);
    }

    /**
     * Indicate that the lease has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subYears(2),
            'end_date' => now()->subMonths(1),
            'is_old' => true,
        ]);
    }

    /**
     * Indicate that the lease is upcoming (hasn't started yet).
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->addMonths(1),
            'end_date' => now()->addYears(1)->addMonths(1),
        ]);
    }

    /**
     * Indicate that the lease is expiring soon.
     */
    public function expiringSoon(int $days = 30): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subMonths(11),
            'end_date' => now()->addDays($days),
            'is_move_out' => false,
        ]);
    }

    /**
     * Indicate that this is a sublease.
     */
    public function sublease(?Lease $parentLease = null): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sub_lease' => true,
            'parent_lease_id' => $parentLease?->id ?? Lease::factory(),
        ]);
    }

    /**
     * Indicate that the lease has been terminated (move-out).
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_move_out' => true,
            'actual_end_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the lease was renewed.
     */
    public function renewed(?Lease $newLease = null): static
    {
        return $this->state(fn (array $attributes) => [
            'is_renew' => true,
            'end_date' => now()->subMonths(1),
        ]);
    }

    /**
     * Indicate that this is a residential lease.
     */
    public function residential(): static
    {
        return $this->state(fn (array $attributes) => [
            'lease_unit_type_id' => 2,
            'tenant_type' => 'individual',
        ]);
    }

    /**
     * Indicate that this is a commercial lease.
     */
    public function commercial(): static
    {
        return $this->state(fn (array $attributes) => [
            'lease_unit_type_id' => 1,
            'tenant_type' => 'corporate',
        ]);
    }

    /**
     * Indicate that the lease has escalations.
     */
    public function withEscalations(): static
    {
        return $this->state(fn (array $attributes) => [
            'lease_escalations_type' => 'percentage',
            'lease_escalations' => [
                ['year' => 2, 'percentage' => 5],
                ['year' => 3, 'percentage' => 5],
            ],
        ]);
    }

    /**
     * Indicate that the lease has additional fees.
     */
    public function withAdditionalFees(): static
    {
        return $this->state(fn (array $attributes) => [
            'additional_fees_lease' => [
                ['name' => 'Maintenance Fee', 'amount' => 5000],
                ['name' => 'Service Fee', 'amount' => 2000],
            ],
        ]);
    }

    /**
     * Set specific tenant for the lease.
     */
    public function forTenant(int $tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId,
        ]);
    }

    /**
     * Set specific community for the lease.
     */
    public function forCommunity(int $communityId): static
    {
        return $this->state(fn (array $attributes) => [
            'community_id' => $communityId,
        ]);
    }

    /**
     * Set specific building for the lease.
     */
    public function forBuilding(int $buildingId): static
    {
        return $this->state(fn (array $attributes) => [
            'building_id' => $buildingId,
        ]);
    }
}
