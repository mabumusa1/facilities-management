<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_record_id')->constrained('rf_document_records')->cascadeOnDelete();
            $table->string('signer_name');
            $table->string('signer_email');
            $table->timestamp('signed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->string('signed_file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_document_signatures');
    }
};
