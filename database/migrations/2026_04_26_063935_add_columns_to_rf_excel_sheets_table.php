<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_excel_sheets', function (Blueprint $table) {
            $table->string('import_type')->nullable()->after('type');
            $table->json('column_schema')->nullable()->after('import_type');
            $table->string('template_file_path')->nullable()->after('column_schema');
        });
    }

    public function down(): void
    {
        Schema::table('rf_excel_sheets', function (Blueprint $table) {
            $table->dropColumn(['import_type', 'column_schema', 'template_file_path']);
        });
    }
};
