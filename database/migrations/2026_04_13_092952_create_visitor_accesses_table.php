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
        Schema::create('visitor_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->cascadeOnDelete();
            $table->foreignId('community_id')->nullable()->constrained('communities')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses');

            // Visitor details
            $table->string('visitor_name');
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->string('visitor_id_number')->nullable();
            $table->string('visitor_vehicle_plate')->nullable();

            // Visit details
            $table->date('visit_start_date');
            $table->time('visit_start_time')->nullable();
            $table->date('visit_end_date')->nullable();
            $table->time('visit_end_time')->nullable();
            $table->enum('access_type', ['one-time', 'recurring', 'permanent'])->default('one-time');
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status_id']);
            $table->index(['unit_id', 'visit_start_date']);
            $table->index('visitor_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_accesses');
    }
};
