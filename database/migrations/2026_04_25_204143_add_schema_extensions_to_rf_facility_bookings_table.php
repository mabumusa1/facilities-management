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
        Schema::table('rf_facility_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('account_tenant_id')->nullable()->after('facility_id')->index();
            $table->string('booked_by_type')->nullable()->after('account_tenant_id'); // resident|admin
            $table->timestamp('start_at')->nullable()->after('booked_by_type');
            $table->timestamp('end_at')->nullable()->after('start_at');
            $table->timestamp('cancelled_at')->nullable()->after('approved_at');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            $table->string('cancellation_by_type')->nullable()->after('cancellation_reason'); // resident|admin
            $table->unsignedBigInteger('invoice_id')->nullable()->after('cancellation_by_type');
            $table->unsignedBigInteger('contract_document_id')->nullable()->after('invoice_id');

            // Calendar query index: facility + time range + status
            $table->index(['facility_id', 'start_at', 'status_id'], 'rf_facility_bookings_calendar_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_facility_bookings', function (Blueprint $table) {
            $table->dropIndex('rf_facility_bookings_calendar_idx');
            $table->dropIndex(['account_tenant_id']);
            $table->dropColumn([
                'account_tenant_id',
                'booked_by_type',
                'start_at',
                'end_at',
                'cancelled_at',
                'cancellation_reason',
                'cancellation_by_type',
                'invoice_id',
                'contract_document_id',
            ]);
        });
    }
};
