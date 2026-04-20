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
        Schema::create('rf_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('rf_request_categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('rf_request_subcategories')->nullOnDelete();
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->morphs('requester');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->string('priority')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_requests');
    }
};
