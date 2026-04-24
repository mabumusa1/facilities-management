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
        Schema::table('rf_professionals', function (Blueprint $table) {
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('first_name_ar')->nullable()->after('first_name');
            $table->string('last_name_ar')->nullable()->after('last_name');
            $table->string('id_type')->nullable()->after('national_id');
            $table->string('national_phone_number')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_professionals', function (Blueprint $table) {
            $table->dropColumn(['first_name_ar', 'last_name_ar', 'id_type', 'national_phone_number']);
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });
    }
};
