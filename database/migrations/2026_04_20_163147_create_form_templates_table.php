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
        Schema::create('rf_form_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('request_category_id')->nullable()->constrained('rf_request_categories')->nullOnDelete();
            $table->foreignId('community_id')->nullable()->constrained('rf_communities')->nullOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('rf_buildings')->nullOnDelete();
            $table->json('schema')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_form_templates');
    }
};
