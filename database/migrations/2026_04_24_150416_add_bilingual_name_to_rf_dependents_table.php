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
        Schema::table('rf_dependents', function (Blueprint $table) {
            $table->string('first_name')->nullable()->change();
            $table->string('first_name_ar')->nullable()->after('first_name');
            $table->string('last_name_ar')->nullable()->after('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_dependents', function (Blueprint $table) {
            $table->dropColumn(['first_name_ar', 'last_name_ar']);
            $table->string('first_name')->nullable(false)->change();
        });
    }
};
