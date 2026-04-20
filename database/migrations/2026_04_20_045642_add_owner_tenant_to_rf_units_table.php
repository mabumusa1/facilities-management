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
        Schema::table('rf_units', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('district_id');
            $table->unsignedBigInteger('tenant_id')->nullable()->after('owner_id');
        });
    }

    public function down(): void
    {
        Schema::table('rf_units', function (Blueprint $table) {
            $table->dropColumn(['owner_id', 'tenant_id']);
        });
    }
};
