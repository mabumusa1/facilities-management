<?php

namespace Database\Factories;

use App\Models\DocumentTemplate;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentVersion>
 */
class DocumentVersionFactory extends Factory
{
    protected $model = DocumentVersion::class;

    public function definition(): array
    {
        return [
            'document_template_id' => DocumentTemplate::factory(),
            'version_number' => fake()->numberBetween(1, 10),
            'body' => fake()->paragraph(),
            'merge_fields' => ['recipient_name', 'date', 'amount'],
            'published_at' => fake()->dateTime(),
            'created_by' => User::factory(),
        ];
    }
}
