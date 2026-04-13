<?php

namespace Database\Factories;

use App\Models\Contact;
// use App\Models\Lease; // Uncomment when Lease model is created
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionSubcategory;
use App\Models\TransactionType;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 1000, 50000);

        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => TransactionCategory::inRandomOrder()->first()->id ?? 1,
            'subcategory_id' => null,
            'type_id' => TransactionType::inRandomOrder()->first()->id ?? 1,
            'status_id' => 1, // Will be set by status trait later
            'unit_id' => null,
            'lease_id' => null,
            'assignee_id' => Contact::factory()->tenant(),
            'amount' => $amount,
            'tax_amount' => $amount * 0.15,
            'rental_amount' => $amount * 0.85,
            'additional_fees_amount' => 0,
            'vat' => 15,
            'paid' => 0,
            'left' => $amount,
            'lease_number' => $this->faker->unique()->regexify('[0-9]{10}RL'),
            'details' => $this->faker->optional()->sentence(),
            'additional_fees' => [],
            'images' => null,
            'due_on' => $this->faker->dateTimeBetween('now', '+90 days'),
            'is_paid' => false,
            'is_old' => false,
            'assignee_active' => true,
        ];
    }

    /**
     * Indicate that the transaction is for a rental.
     */
    public function rental(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => 1, // Rentals
            'rental_amount' => $attributes['amount'],
        ]);
    }

    /**
     * Indicate that the transaction is for insurance refund.
     */
    public function insuranceRefund(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => 19, // Insurance Refund
            'rental_amount' => 0,
        ]);
    }

    /**
     * Indicate that the transaction is paid.
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => 1, // Paid
                'status_id' => 1, // Paid status
                'is_paid' => true,
                'paid' => $attributes['amount'],
                'left' => 0,
            ];
        });
    }

    /**
     * Indicate that the transaction is unpaid/due.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => 2, // Due
            'status_id' => 2, // Due status
            'is_paid' => false,
            'paid' => 0,
            'left' => $attributes['amount'],
        ]);
    }

    /**
     * Indicate that the transaction is partially paid.
     */
    public function partiallyPaid(): static
    {
        return $this->state(function (array $attributes) {
            $paidAmount = $attributes['amount'] * $this->faker->randomFloat(2, 0.2, 0.8);

            return [
                'type_id' => 2, // Due
                'status_id' => 2, // Due status
                'is_paid' => false,
                'paid' => $paidAmount,
                'left' => $attributes['amount'] - $paidAmount,
            ];
        });
    }

    /**
     * Indicate that the transaction is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => 2, // Due
            'status_id' => 2, // Due status
            'due_on' => $this->faker->dateTimeBetween('-90 days', '-1 day'),
            'is_paid' => false,
            'is_old' => true,
        ]);
    }

    /**
     * Indicate that the transaction is due today.
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => 2, // Due
            'status_id' => 2, // Due status
            'due_on' => now(),
            'is_paid' => false,
        ]);
    }

    /**
     * Indicate that the transaction is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => 2, // Due
            'status_id' => 2, // Due status
            'due_on' => $this->faker->dateTimeBetween('+1 day', '+90 days'),
            'is_paid' => false,
        ]);
    }

    /**
     * Indicate that the transaction has additional fees.
     */
    public function withAdditionalFees(): static
    {
        return $this->state(function (array $attributes) {
            $fees = [
                [
                    'name' => $this->faker->words(2, true),
                    'amount' => $this->faker->randomFloat(2, 100, 1000),
                ],
            ];
            $feesAmount = collect($fees)->sum('amount');

            return [
                'additional_fees' => $fees,
                'additional_fees_amount' => $feesAmount,
                'amount' => $attributes['amount'] + $feesAmount,
                'left' => $attributes['amount'] + $feesAmount,
            ];
        });
    }

    /**
     * Indicate that the transaction is linked to a unit.
     */
    public function forUnit(?int $unitId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_id' => $unitId ?? Unit::factory(),
        ]);
    }

    /**
     * Indicate that the transaction is linked to a lease.
     * Uncomment when Lease model is created
     */
    // public function forLease(?int $leaseId = null): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'lease_id' => $leaseId ?? Lease::factory(),
    //     ]);
    // }

    /**
     * Indicate that the transaction has a subcategory.
     */
    public function withSubcategory(?int $subcategoryId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'subcategory_id' => $subcategoryId ?? TransactionSubcategory::factory(),
        ]);
    }

    /**
     * Indicate that the assignee is inactive.
     */
    public function assigneeInactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'assignee_active' => false,
        ]);
    }
}
