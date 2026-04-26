<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stores KYC documents uploaded for a lease application.
     * Uses a dedicated table rather than the generic media table to support
     * document-type tracking and required/optional classification.
     */
    public function up(): void
    {
        Schema::create('lease_kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();

            // FK to rf_settings row of type=kyc_document_type.
            $table->foreignId('document_type_id')->constrained('rf_settings')->restrictOnDelete();

            $table->boolean('is_required')->default(false);
            $table->string('original_file_name');
            $table->string('stored_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_kyc_documents');
    }
};
