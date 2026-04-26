<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a UNIQUE constraint on rf_leases.quote_id to prevent duplicate
     * leases being created from the same quote under concurrent requests.
     * NULL values are exempt from the unique constraint (leases without a quote).
     */
    public function up(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->unique('quote_id');
        });
    }

    public function down(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->dropUnique(['quote_id']);
        });
    }
};
