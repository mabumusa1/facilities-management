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

    public function test_generate_with_receipt_template_type(): void
    {
        $tenant = Tenant::create(['name' => 'Receipt Corp']);
        $tenant->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Payment Receipt', 'ar' => ''],
            'type' => 'receipt',
            'status' => 'active',
            'format' => 'in_platform',
            'created_by' => User::factory()->create()->id,
        ]);

        DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Receipt for {{payer}} — {{amount}}', 'ar' => '']),
            'merge_fields' => [
                ['key' => 'payer', 'label_en' => 'Payer', 'label_ar' => null, 'type' => 'string', 'source_path' => 'payment.payer'],
                ['key' => 'amount', 'label_en' => 'Amount', 'label_ar' => null, 'type' => 'currency', 'source_path' => 'payment.amount'],
            ],
            'published_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        $result = (new DocumentGenerator)->generate('receipt', 'payment', 42, 'en', [
            'payer' => 'Acme Inc',
            'amount' => '1,000 SAR',
        ]);

        $this->assertSame('draft', $result['status']);
        $this->assertEmpty($result['errors']);
        $this->assertGreaterThan(0, $result['document_record_id']);

        $record = DocumentRecord::find($result['document_record_id']);
        $this->assertStringContainsString('Acme Inc', $record->file_path);
        $this->assertStringContainsString('1,000 SAR', $record->file_path);
        $this->assertSame('payment', $record->source_type);
        $this->assertSame(42, $record->source_id);
    }

    public function test_generate_with_booking_template_type(): void
    {
        $tenant = Tenant::create(['name' => 'Booking LLC']);
        $tenant->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Facility Booking Contract', 'ar' => ''],
            'type' => 'booking',
            'status' => 'active',
            'format' => 'in_platform',
            'created_by' => User::factory()->create()->id,
        ]);

        DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Facility: {{facility}}, Date: {{date}}', 'ar' => '']),
            'merge_fields' => [
                ['key' => 'facility', 'label_en' => 'Facility', 'label_ar' => null, 'type' => 'string', 'source_path' => 'booking.facility'],
                ['key' => 'date', 'label_en' => 'Date', 'label_ar' => null, 'type' => 'date', 'source_path' => 'booking.date'],
            ],
            'published_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        $result = (new DocumentGenerator)->generate('booking', 'facility_booking', 77, 'en', [
            'facility' => 'Tennis Court A',
            'date' => '2026-05-01',
        ]);

        $this->assertSame('draft', $result['status']);
        $this->assertEmpty($result['errors']);
        $this->assertGreaterThan(0, $result['document_record_id']);

        $record = DocumentRecord::find($result['document_record_id']);
        $this->assertStringContainsString('Tennis Court A', $record->file_path);
        $this->assertStringContainsString('2026-05-01', $record->file_path);
        $this->assertSame('facility_booking', $record->source_type);
        $this->assertSame(77, $record->source_id);
    }

    public function test_generate_fails_with_empty_merge_values(): void
    {
        $this->seedTemplateAndTenant();
        $generator = new DocumentGenerator;

        $result = $generator->generate('lease', 'lease', 99, 'en', []);

        $this->assertSame('error', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame(0, $result['document_record_id']);
        $this->assertDatabaseCount('rf_document_records', 0);
    }

    public function test_generate_fails_with_empty_string_values(): void
    {
        $this->seedTemplateAndTenant();
        $generator = new DocumentGenerator;

        $result = $generator->generate('lease', 'lease', 99, 'en', [
            'name' => '',
            'rent' => '',
        ]);

        $this->assertSame('error', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame(0, $result['document_record_id']);
        $this->assertDatabaseCount('rf_document_records', 0);
    }

    public function test_generate_enforces_tenant_isolation(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A']);
        $tenantA->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenantA->id,
            'name' => ['en' => 'Lease A', 'ar' => ''],
            'type' => 'lease',
            'status' => 'active',
            'format' => 'in_platform',
            'created_by' => User::factory()->create()->id,
        ]);

        DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => '{{name}}', 'ar' => '']),
            'merge_fields' => [['key' => 'name', 'label_en' => 'N', 'label_ar' => null, 'type' => 'string', 'source_path' => 'x']],
            'published_at' => now(),
            'created_by' => User::factory()->create()->id,
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        $tenantB = Tenant::create(['name' => 'Tenant B']);
        $tenantB->makeCurrent();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("No active 'lease' template found.");

        (new DocumentGenerator)->generate('lease', 'lease', 1, 'en', ['name' => 'Test']);
    }

    public function test_generate_fails_when_template_has_no_published_version(): void
    {
        $tenant = Tenant::create(['name' => 'No Version Co']);
        $tenant->makeCurrent();

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Unversioned Template', 'ar' => ''],
            'type' => 'lease',
            'status' => 'active',
            'format' => 'in_platform',
            'current_version_id' => null,
            'created_by' => User::factory()->create()->id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Template 'Unversioned Template' has no published version.");

        (new DocumentGenerator)->generate('lease', 'lease', 1, 'en', ['name' => 'Test']);
    }
}
