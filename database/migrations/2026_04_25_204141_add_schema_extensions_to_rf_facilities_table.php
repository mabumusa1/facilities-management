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
        Schema::table('rf_facilities', function (Blueprint $table) {
            $table->string('currency', 3)->default('SAR')->after('booking_fee');
            $table->string('type')->default('other')->after('currency'); // gym|pool|hall|court|other
            $table->string('pricing_mode')->default('free')->after('type'); // free|per_session|per_hour
            $table->boolean('requires_booking')->default(false)->after('pricing_mode');
            $table->integer('booking_horizon_days')->default(14)->after('requires_booking');
            $table->integer('cancellation_hours_before')->default(2)->after('booking_horizon_days');
            $table->integer('min_booking_duration_minutes')->default(30)->after('cancellation_hours_before');
            $table->integer('max_booking_duration_minutes')->nullable()->after('min_booking_duration_minutes');
            $table->boolean('contract_required')->default(false)->after('max_booking_duration_minutes');
            $table->text('notes')->nullable()->after('contract_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_facilities', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'type',
                'pricing_mode',
                'requires_booking',
                'booking_horizon_days',
                'cancellation_hours_before',
                'min_booking_duration_minutes',
                'max_booking_duration_minutes',
                'contract_required',
                'notes',
            ]);
        });
    }
};
