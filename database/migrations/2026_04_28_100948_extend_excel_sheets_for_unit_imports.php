<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_excel_sheets', function (Blueprint $table): void {
            $table->unsignedInteger('total_rows')->nullable()->after('status');
            $table->unsignedInteger('success_count')->nullable()->after('total_rows');
            $table->unsignedInteger('error_count')->nullable()->after('success_count');
            $table->json('meta')->nullable()->after('error_details');
        });
    }

    public function down(): void
    {
        Schema::table('rf_excel_sheets', function (Blueprint $table): void {
            $table->dropColumn(['total_rows', 'success_count', 'error_count', 'meta']);
        });
    }
};
