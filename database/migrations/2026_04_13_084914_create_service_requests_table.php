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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            // Category & Type
            $table->foreignId('category_id')->constrained('service_request_categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('service_request_subcategories')->nullOnDelete();

            // Status
            $table->foreignId('status_id')->constrained('statuses');

            // Property Relations
            $table->foreignId('community_id')->nullable()->constrained('communities')->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();

            // People Relations
            $table->foreignId('requester_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('requester_type')->comment('Owner, Tenant, Admin');
            $table->foreignId('professional_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('contacts')->nullOnDelete();

            // Request Details
            $table->string('request_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Scheduling
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            // Financial
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('SAR');

            // Additional Data
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('professional_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Ratings & Feedback
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('feedback')->nullable();

            // System Fields
            $table->foreignId('created_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('request_number');
            $table->index('status_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('requester_id');
            $table->index('professional_id');
            $table->index('unit_id');
            $table->index('scheduled_date');
            $table->index('priority');
            $table->index(['status_id', 'scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
