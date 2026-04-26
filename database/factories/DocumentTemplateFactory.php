<?php

namespace Database\Factories;

use App\Models\DocumentTemplate;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentTemplate>
 */
class DocumentTemplateFactory extends Factory
{
    protected $model = DocumentTemplate::class;

    public function definition(): array
    {
        return [
            'account_tenant_id' => fn () => Tenant::create(['name' => fake()->unique()->company()]),
            'name' => ['en' => fake()->unique()->word(), 'ar' => 'قالب'],
            'type' => fake()->randomElement(['lease', 'booking', 'invoice', 'receipt', 'custom']),
            'status' => fake()->randomElement(['draft', 'active', 'archived']),
            'format' => fake()->randomElement(['word_upload', 'in_platform']),
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
