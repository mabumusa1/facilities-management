<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add assigned_to_user_id to rf_requests.
     *
     * This supports the admin triage story (#211): assigning a service request
     * directly to a User (manager/technician) rather than a Professional record.
     */
    public function up(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->after('professional_id')
                ->index()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_user_id']);
            $table->dropIndex(['assigned_to_user_id']);
            $table->dropColumn('assigned_to_user_id');
        });
    }
};
