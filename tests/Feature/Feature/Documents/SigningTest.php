<?php

namespace Tests\Feature\Feature\Documents;

use App\Models\DocumentRecord;
use App\Models\DocumentSignature;
use App\Models\DocumentTemplate;
use App\Models\DocumentVersion;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class SigningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function seedRecord(string $status = 'draft'): DocumentRecord
    {
        Role::firstOrCreate(['name' => 'accountAdmins', 'guard_name' => 'web']);

        $tenant = Tenant::create(['name' => 'Sign Test ' . Str::random(4)]);
        $tenant->makeCurrent();

        $user = User::factory()->create();
        $user->assignRole('accountAdmins');
        $this->actingAs($user);

        $template = DocumentTemplate::create([
            'account_tenant_id' => $tenant->id,
            'name' => ['en' => 'Sign Template', 'ar' => null],
            'type' => 'lease',
            'status' => 'active',
            'format' => 'in_platform',
            'created_by' => $user->id,
        ]);
        DocumentVersion::create([
            'document_template_id' => $template->id,
            'version_number' => 1,
            'body' => json_encode(['en' => 'Contract for {{name}}', 'ar' => '']),
            'merge_fields' => [['key' => 'name', 'label_en' => 'Name', 'label_ar' => null, 'type' => 'string', 'source_path' => 'x']],
            'published_at' => now(),
            'created_by' => $user->id,
        ]);
        $template->update(['current_version_id' => DocumentVersion::first()->id]);

        $token = $status !== 'draft' ? Str::random(64) : null;

        return DocumentRecord::create([
            'account_tenant_id' => $tenant->id,
            'document_template_version_id' => DocumentVersion::first()->id,
            'source_type' => 'lease',
            'source_id' => 1,
            'status' => $status,
            'signing_token' => $token,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    public function test_send_for_signature_creates_token(): void
    {
        $record = $this->seedRecord('draft');

        $response = $this->post("/admin/documents/records/{$record->id}/send", [
            'signer_name' => 'John Signer',
            'signer_email' => 'john@example.com',
        ]);

        $response->assertRedirect();

        $record->refresh();
        $this->assertSame('sent', $record->status);
        $this->assertNotNull($record->signing_token);
        $this->assertNotNull($record->sent_at);
    }

    public function test_signing_page_loads_with_token(): void
    {
        $this->seedRecord('sent');
        $record = DocumentRecord::first();

        $response = $this->get("/sign/{$record->signing_token}");

        $response->assertOk();
    }

    public function test_signing_page_rejects_invalid_token(): void
    {
        $response = $this->get('/sign/invalid-token-123');

        $this->assertNotEquals(200, $response->status());
    }

    public function test_signing_page_rejects_expired_link(): void
    {
        $record = $this->seedRecord('draft');
        $record->update(['status' => 'sent', 'signing_token' => 'exp-token', 'sent_at' => now()->subDays(8)]);

        $response = $this->get('/sign/exp-token');

        $this->assertNotEquals(200, $response->status());
    }

    public function test_request_otp_returns_otp(): void
    {
        $record = $this->seedRecord('sent');
        DocumentSignature::create([
            'document_record_id' => $record->id,
            'signer_name' => 'John',
            'signer_email' => 'john@example.com',
        ]);

        $response = $this->post("/sign/{$record->signing_token}/otp");

        $response->assertOk();
        $this->assertNotNull($response->json('otp'));
    }

    public function test_sign_with_valid_otp_records_signature(): void
    {
        $record = $this->seedRecord('sent');
        DocumentSignature::create([
            'document_record_id' => $record->id,
            'signer_name' => 'John',
            'signer_email' => 'john@example.com',
        ]);

        $otpRes = $this->post("/sign/{$record->signing_token}/otp");
        $otp = $otpRes->json('otp');

        $response = $this->post("/sign/{$record->signing_token}/sign", [
            'otp' => $otp,
            'signer_name' => 'John Doe',
        ]);

        $response->assertOk();
        $record->refresh();
        $this->assertSame('signed', $record->status);
    }

    public function test_sign_with_invalid_otp_fails(): void
    {
        $this->seedRecord('sent');
        $record = DocumentRecord::first();
        DocumentSignature::create([
            'document_record_id' => $record->id,
            'signer_name' => 'John',
            'signer_email' => 'john@example.com',
        ]);

        $response = $this->post("/sign/{$record->signing_token}/sign", [
            'otp' => '000000',
            'signer_name' => 'John',
        ]);

        $response->assertStatus(422);
    }

    public function test_resend_link_updates_token(): void
    {
        $record = $this->seedRecord('sent');

        $response = $this->post("/admin/documents/records/{$record->id}/resend");

        $response->assertRedirect();
        $record->refresh();
        $this->assertNotNull($record->signing_token);
    }

    public function test_cannot_sign_signed_document(): void
    {
        $record = $this->seedRecord('sent');
        $record->update(['status' => 'signed']);

        $response = $this->post("/sign/{$record->signing_token}/sign", [
            'otp' => '123456',
            'signer_name' => 'John',
        ]);

        $response->assertStatus(422);
    }
}
