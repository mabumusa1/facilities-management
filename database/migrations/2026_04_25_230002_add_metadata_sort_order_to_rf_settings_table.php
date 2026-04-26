<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add metadata (JSON) and sort_order to rf_settings for KYC document type configuration.
     */
    public function up(): void
    {
        Schema::table('rf_settings', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('is_default');
            $table->unsignedInteger('sort_order')->default(0)->after('metadata');
        });
    }

    public function down(): void
    {
        Schema::table('rf_settings', function (Blueprint $table) {
            $table->dropColumn(['metadata', 'sort_order']);
        });
    }
};
