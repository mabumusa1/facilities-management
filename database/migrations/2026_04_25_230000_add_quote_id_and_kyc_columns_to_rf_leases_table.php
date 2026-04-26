<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add quote_id back-reference and KYC tracking columns to rf_leases.
     * quote_id is nullable: leases created without a quote stay null.
     */
    public function up(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->unsignedBigInteger('quote_id')->nullable()->index()->after('account_tenant_id');
            $table->boolean('kyc_complete')->default(false)->after('pdf_url');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_complete');
        });
    }

    public function down(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->dropIndex(['quote_id']);
            $table->dropColumn(['quote_id', 'kyc_complete', 'kyc_submitted_at']);
        });
    }
};
