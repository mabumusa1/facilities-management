<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_facility_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('rf_facility_bookings', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('contract_document_id');
            }
            if (! Schema::hasColumn('rf_facility_bookings', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            }
            if (! Schema::hasColumn('rf_facility_bookings', 'checked_in_by')) {
                $table->unsignedBigInteger('checked_in_by')->nullable()->after('checked_out_at');
            }
            if (! Schema::hasColumn('rf_facility_bookings', 'purpose')) {
                $table->string('purpose')->nullable()->after('notes');
            }
            // Make status_id nullable for new booking creation
            $table->unsignedBigInteger('status_id')->nullable()->change();
        });

        Schema::table('rf_facility_waitlist', function (Blueprint $table) {
            if (! Schema::hasColumn('rf_facility_waitlist', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('resident_id');
            }
            if (! Schema::hasColumn('rf_facility_waitlist', 'facility_id')) {
                $table->unsignedBigInteger('facility_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('rf_facility_waitlist', 'account_tenant_id')) {
                $table->unsignedBigInteger('account_tenant_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('rf_facility_waitlist', 'date')) {
                $table->date('date')->nullable()->after('facility_id');
            }
            if (! Schema::hasColumn('rf_facility_waitlist', 'start_time')) {
                $table->string('start_time', 5)->nullable()->after('date');
            }
            if (! Schema::hasColumn('rf_facility_waitlist', 'end_time')) {
                $table->string('end_time', 5)->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rf_facility_bookings', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'checked_out_at', 'checked_in_by', 'purpose']);
        });
        Schema::table('rf_facility_waitlist', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'facility_id', 'account_tenant_id', 'date', 'start_time', 'end_time']);
        });
    }
};
