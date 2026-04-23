<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the Spatie `permissions` table with `subject` and `action` columns
 * that store the decomposed parts of the composite permission slug
 * (e.g. "leases.VIEW" → subject="leases", action="VIEW").
 *
 * These columns are stored (not computed) to allow efficient indexed filtering
 * such as "all permissions for subject=leases".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('name');
            $table->string('action')->nullable()->after('subject');

            $table->index(['subject', 'action'], 'permissions_subject_action_index');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex('permissions_subject_action_index');
            $table->dropColumn(['subject', 'action']);
        });
    }
};
