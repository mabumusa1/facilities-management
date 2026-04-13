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
        Schema::table('users', function (Blueprint $table) {
            // Scope-based access control fields
            $table->boolean('is_all_communities')->default(false)->after('service_manager_type');
            $table->boolean('is_all_buildings')->default(false)->after('is_all_communities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_all_communities', 'is_all_buildings']);
        });
    }
};
