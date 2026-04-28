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
        Schema::table('rf_units', function (Blueprint $table) {
            $table->foreignId('currency_id')
                ->nullable()
                ->after('marketplace_booking_unit_id')
                ->constrained('currencies')
                ->nullOnDelete();

            $table->decimal('asking_rent_amount', 12, 2)
                ->nullable()
                ->after('currency_id');

            $table->enum('rent_period', ['month', 'year'])
                ->nullable()
                ->after('asking_rent_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_units', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'asking_rent_amount', 'rent_period']);
        });
    }
};
