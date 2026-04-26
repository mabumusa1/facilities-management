<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add approval/rejection tracking columns to rf_leases.
     * Stores the approver/rejector identity (user ID), timestamps, and the rejection reason.
     * Uses the users table (not rf_admins) to be consistent with created_by_id FK on this table.
     */
    public function up(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by_id')->nullable()->index()->after('kyc_submitted_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
            $table->unsignedBigInteger('rejected_by_id')->nullable()->index()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by_id');
            $table->text('rejection_reason')->nullable()->after('rejected_at');

            $table->foreign('approved_by_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rejected_by_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropForeign(['rejected_by_id']);
            $table->dropIndex(['approved_by_id']);
            $table->dropIndex(['rejected_by_id']);
            $table->dropColumn(['approved_by_id', 'approved_at', 'rejected_by_id', 'rejected_at', 'rejection_reason']);
        });
    }
};
