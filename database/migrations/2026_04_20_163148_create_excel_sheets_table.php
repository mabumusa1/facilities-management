<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rf_excel_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('general');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('status')->default('uploaded')->index();
            $table->json('error_details')->nullable();
            $table->foreignId('rf_community_id')->nullable()->constrained('rf_communities')->nullOnDelete();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_excel_sheets');
    }
};
