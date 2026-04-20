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
            $table->foreignId('unit_id')->nullable()->after('status_id')->constrained('rf_units')->nullOnDelete();
            $table->foreignId('community_id')->nullable()->after('unit_id')->constrained('rf_communities')->nullOnDelete();
            $table->foreignId('building_id')->nullable()->after('community_id')->constrained('rf_buildings')->nullOnDelete();
            $table->foreignId('professional_id')->nullable()->after('building_id')->constrained('rf_professionals')->nullOnDelete();
            $table->string('request_code')->nullable()->after('professional_id');
            $table->unsignedBigInteger('account_tenant_id')->nullable()->after('request_code')->index();
            $table->timestamp('assigned_at')->nullable()->after('resolved_at');
            $table->timestamp('completed_at')->nullable()->after('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['community_id']);
            $table->dropForeign(['building_id']);
            $table->dropForeign(['professional_id']);
            $table->dropColumn([
                'unit_id', 'community_id', 'building_id', 'professional_id',
                'request_code', 'account_tenant_id', 'assigned_at', 'completed_at',
            ]);
        });
    }
};
