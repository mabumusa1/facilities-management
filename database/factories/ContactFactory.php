<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'tenant_id' => Tenant::factory(),
            'contact_type' => 'owner',
            'role' => null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'image' => null,
            'georgian_birthdate' => fake()->optional()->date(),
            'gender' => fake()->optional()->randomElement(['male', 'female']),
            'national_id' => fake()->optional()->numerify('##########'),
            'nationality' => fake()->optional()->countryCode(),
            'phone_number' => fake()->e164PhoneNumber(),
            'national_phone_number' => fake()->numerify('05########'),
            'phone_country_code' => 'SA',
            'active' => true,
            'account_creation_date' => now(),
            'last_active' => null,
            'source' => null,
            'accepted_invite' => false,
            'relation' => null,
            'relation_key' => null,
        ];
    }

    // Contact Type States

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_type' => 'owner',
            'source' => null,
            'accepted_invite' => false,
        ]);
    }

    public function tenant(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_type' => 'tenant',
            'source' => fake()->randomElement(['app', 'web', 'portal', 'import']),
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_type' => 'admin',
            'role' => 'Admins',
            'source' => null,
            'accepted_invite' => false,
        ]);
    }

    public function professional(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_type' => 'professional',
            'source' => null,
            'accepted_invite' => false,
        ]);
    }

    // General States

    public function forTenant(Tenant|int $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant instanceof Tenant ? $tenant->id : $tenant,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
            'last_active' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
            'first_name' => fake()->firstNameMale(),
        ]);
    }

    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
            'first_name' => fake()->firstNameFemale(),
        ]);
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => fake()->imageUrl(200, 200, 'people'),
        ]);
    }

    public function withNationalId(): static
    {
        return $this->state(fn (array $attributes) => [
            'national_id' => fake()->numerify('##########'),
        ]);
    }

    public function withNationality(): static
    {
        return $this->state(fn (array $attributes) => [
            'nationality' => fake()->countryCode(),
        ]);
    }

    public function withBirthdate(): static
    {
        return $this->state(fn (array $attributes) => [
            'georgian_birthdate' => fake()->date(),
        ]);
    }

    // Tenant-specific States

    public function invited(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'app',
            'accepted_invite' => false,
        ]);
    }

    public function inviteAccepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'app',
            'accepted_invite' => true,
            'last_active' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function fromImport(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'import',
        ]);
    }

    public function fromWeb(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'web',
        ]);
    }

    public function fromPortal(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'portal',
        ]);
    }
}
