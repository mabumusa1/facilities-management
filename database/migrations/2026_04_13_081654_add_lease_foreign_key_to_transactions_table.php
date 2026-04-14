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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('transaction_categories')->cascadeOnDelete();
            $table->foreign('subcategory_id')->references('id')->on('transaction_subcategories')->nullOnDelete();
            $table->foreign('type_id')->references('id')->on('transaction_types')->cascadeOnDelete();
            $table->foreign('lease_id')->references('id')->on('leases')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropForeign(['type_id']);
            $table->dropForeign(['lease_id']);
        });
    }
};
