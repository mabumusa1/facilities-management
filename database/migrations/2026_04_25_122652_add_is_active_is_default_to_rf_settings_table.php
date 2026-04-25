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
        Schema::table('rf_settings', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('parent_id');
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->string('subtype')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_settings', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_default', 'subtype']);
        });
    }
};
