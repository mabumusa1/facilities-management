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
        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            // Booking details
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->text('special_requests')->nullable();

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Cancellation tracking
            $table->timestamp('canceled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Check-in/Check-out tracking
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->timestamp('checked_out_at')->nullable();
            $table->foreignId('checked_out_by')->nullable()->constrained('contacts')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['facility_id', 'booking_date']);
            $table->index(['contact_id', 'booking_date']);
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_bookings');
    }
};
