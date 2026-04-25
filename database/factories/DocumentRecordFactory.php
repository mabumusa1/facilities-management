<?php

namespace Database\Factories;

use App\Models\DocumentRecord;
use App\Models\DocumentVersion;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentRecord>
 */
class DocumentRecordFactory extends Factory
{
    protected $model = DocumentRecord::class;

    public function definition(): array
    {
        return [
            'account_tenant_id' => fn () => Tenant::create(['name' => fake()->unique()->company()]),
            'document_template_version_id' => DocumentVersion::factory(),
            'generated_at' => fake()->dateTime(),
            'file_path' => fake()->filePath(),
            'status' => fake()->randomElement(['draft', 'sent', 'signed', 'archived']),
        ];
    }
}
