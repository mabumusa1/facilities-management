<?php

namespace Tests\Feature\Feature\Documents;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DocumentDataModelTest extends TestCase
{
    public function test_document_templates_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('rf_document_templates'));
        $this->assertTrue(Schema::hasColumns('rf_document_templates', [
            'id', 'account_tenant_id', 'name', 'type', 'status', 'format',
            'created_by', 'current_version_id', 'created_at', 'updated_at', 'deleted_at',
        ]));
    }

    public function test_document_versions_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('rf_document_versions'));
        $this->assertTrue(Schema::hasColumns('rf_document_versions', [
            'id', 'document_template_id', 'version_number', 'body', 'file_path',
            'merge_fields', 'published_at', 'created_by', 'created_at', 'updated_at',
        ]));
    }

    public function test_document_records_table_exists_with_polymorphic_source(): void
    {
        $this->assertTrue(Schema::hasTable('rf_document_records'));
        $this->assertTrue(Schema::hasColumns('rf_document_records', [
            'id', 'account_tenant_id', 'document_template_version_id',
            'source_type', 'source_id', 'generated_at', 'file_path', 'status',
            'created_at', 'updated_at', 'deleted_at',
        ]));
    }

    public function test_document_signatures_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('rf_document_signatures'));
        $this->assertTrue(Schema::hasColumns('rf_document_signatures', [
            'id', 'document_record_id', 'signer_name', 'signer_email',
            'signed_at', 'ip_address', 'otp_verified_at', 'signed_file_path',
            'created_at', 'updated_at',
        ]));
    }

    public function test_document_template_has_localized_name_column(): void
    {
        $this->assertSame('json', Schema::getColumnType('rf_document_templates', 'name'));
    }

    public function test_document_version_merge_fields_is_json(): void
    {
        $this->assertSame('json', Schema::getColumnType('rf_document_versions', 'merge_fields'));
    }
}
