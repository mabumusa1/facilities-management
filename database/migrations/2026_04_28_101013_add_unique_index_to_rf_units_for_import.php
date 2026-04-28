<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_units', function (Blueprint $table): void {
            // Prevent duplicate unit names within the same building
            $table->unique(['rf_building_id', 'name'], 'rf_units_building_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rf_units', function (Blueprint $table): void {
            $table->dropUnique('rf_units_building_name_unique');
        });
    }
};
