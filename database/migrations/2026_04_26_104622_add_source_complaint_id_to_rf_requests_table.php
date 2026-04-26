<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('source_complaint_id')->nullable()->after('request_code');
        });
    }

    public function down(): void
    {
        Schema::table('rf_requests', function (Blueprint $table) {
            $table->dropColumn('source_complaint_id');
        });
    }
};
