<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_leads', function (Blueprint $table): void {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('phone_country_code', 5)->nullable()->after('phone_number');
            $table->text('notes')->nullable()->after('phone_country_code');
        });
    }

    public function down(): void
    {
        Schema::table('rf_leads', function (Blueprint $table): void {
            $table->dropColumn(['name_en', 'name_ar', 'phone_country_code', 'notes']);
        });
    }
};
