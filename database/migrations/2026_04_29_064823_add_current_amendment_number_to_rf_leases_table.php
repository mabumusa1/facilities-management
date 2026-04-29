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
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->unsignedInteger('current_amendment_number')->default(0)->after('quote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_leases', function (Blueprint $table) {
            $table->dropColumn('current_amendment_number');
        });
    }
};
