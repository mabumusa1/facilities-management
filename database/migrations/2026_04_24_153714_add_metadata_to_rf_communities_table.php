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
        Schema::table('rf_communities', function (Blueprint $table) {
            $table->json('working_days')->nullable()->after('map');
            $table->decimal('latitude', 10, 7)->nullable()->after('working_days');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_communities', function (Blueprint $table) {
            $table->dropColumn(['working_days', 'latitude', 'longitude']);
        });
    }
};
