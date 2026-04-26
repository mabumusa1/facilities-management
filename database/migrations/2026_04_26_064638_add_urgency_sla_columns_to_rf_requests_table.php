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
            $table->string('urgency')->default('normal')->after('request_code');
            $table->string('room_location')->nullable()->after('urgency');
            $table->timestamp('sla_response_due_at')->nullable()->after('room_location');
            $table->timestamp('sla_resolution_due_at')->nullable()->after('sla_response_due_at');
            // service_category_id was added by #209 migration; only add service_subcategory_id here.
            $table->foreignId('service_subcategory_id')
                ->nullable()
                ->after('service_category_id')
                ->constrained('service_subcategories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->dropForeign(['service_subcategory_id']);
            $table->dropColumn('service_subcategory_id');
            $table->dropColumn(['urgency', 'room_location', 'sla_response_due_at', 'sla_resolution_due_at']);
        });
    }
};
