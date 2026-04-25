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
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->date('scheduled_date')->nullable()->after('request_code');
            $table->date('completed_date')->nullable()->after('scheduled_date');

            $table->index(['account_tenant_id', 'status_id'], 'rf_requests_tenant_status_index');
            $table->index(['account_tenant_id', 'created_at'], 'rf_requests_tenant_created_at_index');
            $table->unique(['account_tenant_id', 'request_code'], 'rf_requests_tenant_request_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->dropIndex('rf_requests_tenant_status_index');
            $table->dropIndex('rf_requests_tenant_created_at_index');
            $table->dropUnique('rf_requests_tenant_request_code_unique');
            $table->dropColumn(['scheduled_date', 'completed_date']);
        });
    }
};
