<?php

namespace Tests\Feature\Feature\Documents;

use App\Models\DocumentRecord;
use App\Models\DocumentTemplate;
use App\Models\DocumentVersion;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DocumentGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function seedTemplateAndTenant(): DocumentTemplate
    {
        $tenant = Tenant::create(['name' => 'Test Gen']);
        $tenant->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Lease Agreement', 'ar' => 'عقد إيجار'],
            'type' => 'lease',
            'status' => 'active',
            'format' => 'in_platform',
            'created_by' => User::factory()->create()->id,
        ]);

        DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Tenant: {{name}}, Rent: {{rent}}', 'ar' => '']),
            'merge_fields' => [
                ['key' => 'name', 'label_en' => 'Tenant Name', 'label_ar' => null, 'type' => 'string', 'source_path' => 'resident.name'],
                ['key' => 'rent', 'label_en' => 'Rent Amount', 'label_ar' => null, 'type' => 'currency', 'source_path' => 'lease.rent'],
            ],
            'published_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        return $template->fresh();
    }

    public function test_generate_creates_document_record(): void
    {
        $this->seedTemplateAndTenant();
        $generator = new DocumentGenerator;

        $result = $generator->generate('lease', 'lease', 99, 'en', [
            'name' => 'Sarah Ahmad',
            'rent' => '4,500 SAR',
        ]);

        $this->assertSame('draft', $result['status']);
        $this->assertEmpty($result['errors']);
        $this->assertGreaterThan(0, $result['document_record_id']);

        $record = DocumentRecord::find($result['document_record_id']);
        $this->assertNotNull($record);
        $this->assertStringContainsString('Sarah Ahmad', $record->file_path);
        $this->assertSame('lease', $record->source_type);
        $this->assertSame(99, $record->source_id);
    }

    public function test_generate_fails_on_missing_merge_field(): void
    {
        $this->seedTemplateAndTenant();
        $generator = new DocumentGenerator;

        $result = $generator->generate('lease', 'lease', 99, 'en', [
            'name' => 'Sarah Ahmad',
            // 'rent' missing
        ]);

        $this->assertSame('error', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame(0, $result['document_record_id']);

        $this->assertDatabaseCount('rf_document_records', 0);
    }

    public function test_generate_requires_active_template(): void
    {
        Tenant::create(['name' => 'T'])->makeCurrent();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("No active 'lease' template found.");

        (new DocumentGenerator)->generate('lease', 'lease', 1, 'en', []);
    }

    public function test_generate_pins_to_version_at_generation_time(): void
    {
        $template = $this->seedTemplateAndTenant();
        $firstVersionId = $template->current_version_id;

        // Create a second version
        $v2 = DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 2,
            'body' => json_encode(['en' => 'V2: {{name}}', 'ar' => '']),
            'merge_fields' => [['key' => 'name', 'label_en' => 'N', 'label_ar' => null, 'type' => 'string', 'source_path' => 'x']],
            'published_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
        $template->update(['current_version_id' => $v2->id]);

        $generator = new DocumentGenerator;
        $result = $generator->generate('lease', 'lease', 1, 'en', ['name' => 'Test']);

        $record = DocumentRecord::find($result['document_record_id']);
        $this->assertSame($v2->id, $record->document_template_version_id);
    }

    public function test_generate_uses_arabic_locale(): void
    {
        $tenant = Tenant::create(['name' => 'AR Test']);
        $tenant->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Invoice', 'ar' => 'فاتورة'],
            'type' => 'invoice',
            'status' => 'active',
            'format' => 'in_platform',
            'created_by' => User::factory()->create()->id,
        ]);

        DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Amount: {{amt}}', 'ar' => 'المبلغ: {{amt}}']),
            'merge_fields' => [['key' => 'amt', 'label_en' => 'Amount', 'label_ar' => 'المبلغ', 'type' => 'currency', 'source_path' => 'invoice.amount']],
            'published_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        $generator = new DocumentGenerator;
        $result = $generator->generate('invoice', 'invoice', 1, 'ar', ['amt' => '١٠٠٠']);

        $record = DocumentRecord::find($result['document_record_id']);
        $this->assertStringContainsString('المبلغ', $record->file_path);
        $this->assertStringContainsString('١٠٠٠', $record->file_path);
    }
}
