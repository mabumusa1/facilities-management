<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('rf_requests', 'sla_response_due_at')) {
                $table->timestamp('sla_response_due_at')->nullable()->after('completed_date');
            }
            if (! Schema::hasColumn('rf_requests', 'sla_resolution_due_at')) {
                $table->timestamp('sla_resolution_due_at')->nullable()->after('sla_response_due_at');
            }
            if (! Schema::hasColumn('rf_requests', 'sla_breach_response')) {
                $table->boolean('sla_breach_response')->default(false)->after('sla_resolution_due_at');
            }
            if (! Schema::hasColumn('rf_requests', 'sla_breach_resolution')) {
                $table->boolean('sla_breach_resolution')->default(false)->after('sla_breach_response');
            }
            if (! Schema::hasColumn('rf_requests', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('sla_breach_resolution');
            }
            if (! Schema::hasColumn('rf_requests', 'feedback')) {
                $table->text('feedback')->nullable()->after('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->dropColumn([
                'sla_response_due_at', 'sla_resolution_due_at',
                'sla_breach_response', 'sla_breach_resolution',
                'rating', 'feedback',
            ]);
        });
    }
};
