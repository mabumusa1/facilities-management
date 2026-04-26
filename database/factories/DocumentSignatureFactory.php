<?php

namespace Database\Factories;

use App\Models\DocumentRecord;
use App\Models\DocumentSignature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentSignature>
 */
class DocumentSignatureFactory extends Factory
{
    protected $model = DocumentSignature::class;

    public function definition(): array
    {
        return [
            'document_record_id' => DocumentRecord::factory(),
            'signer_name' => fake()->name(),
            'signer_email' => fake()->safeEmail(),
            'signed_at' => fake()->dateTime(),
            'ip_address' => fake()->ipv4(),
            'otp_verified_at' => fake()->dateTime(),
            'signed_file_path' => fake()->filePath(),
        ];
    }
}
