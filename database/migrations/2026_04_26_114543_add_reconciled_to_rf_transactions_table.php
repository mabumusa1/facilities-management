<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_transactions', function (Blueprint $table) {
            $table->boolean('is_reconciled')->default(false)->after('notes');
            $table->timestamp('reconciled_at')->nullable()->after('is_reconciled');
            $table->unsignedBigInteger('reconciled_by')->nullable()->after('reconciled_at');
        });
    }

    public function down(): void
    {
        Schema::table('rf_transactions', function (Blueprint $table) {
            $table->dropColumn(['is_reconciled', 'reconciled_at', 'reconciled_by']);
        });
    }
};
