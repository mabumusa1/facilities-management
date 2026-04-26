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
        Schema::table('rf_transactions', function (Blueprint $table) {
            $table->string('direction', 20)->default('money_in')->after('status_id');
            $table->string('payment_method', 30)->nullable()->after('direction');
            $table->string('reference_number', 100)->nullable()->after('payment_method');
            $table->index('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_transactions', function (Blueprint $table) {
            $table->dropIndex(['direction']);
            $table->dropColumn(['direction', 'payment_method', 'reference_number']);
        });
    }
};
