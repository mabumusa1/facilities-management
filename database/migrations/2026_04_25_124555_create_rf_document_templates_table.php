<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->json('name');
            $table->string('type');
            $table->string('status')->default('draft');
            $table->string('format')->default('word_upload');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('current_version_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_document_templates');
    }
};
