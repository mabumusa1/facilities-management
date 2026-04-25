<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->constrained('rf_document_templates')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->text('body')->nullable();
            $table->string('file_path')->nullable();
            $table->json('merge_fields')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['document_template_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_document_versions');
    }
};
