<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_app_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('rf_app_settings', 'lease_alert_thresholds')) {
                $table->json('lease_alert_thresholds')->nullable()->after('login_bg_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rf_app_settings', function (Blueprint $table) {
            $table->dropColumnIfExists('lease_alert_thresholds');
        });
    }
};
