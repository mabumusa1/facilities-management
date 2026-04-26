<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_document_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('document_template_version_id')->constrained('rf_document_versions')->cascadeOnDelete();
            $table->nullableMorphs('source');
            $table->timestamp('generated_at')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_document_records');
    }
};
