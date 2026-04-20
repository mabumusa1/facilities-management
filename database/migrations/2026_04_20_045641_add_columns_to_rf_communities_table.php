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
        Schema::table('rf_communities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('product_code')->nullable()->after('district_id');
            $table->string('license_number')->nullable()->after('product_code');
            $table->date('license_issue_date')->nullable()->after('license_number');
            $table->date('license_expiry_date')->nullable()->after('license_issue_date');
            $table->integer('completion_percent')->default(0)->after('total_income');
            $table->boolean('allow_cash_sale')->default(false)->after('is_off_plan_sale');
            $table->boolean('allow_bank_financing')->default(false)->after('allow_cash_sale');
            $table->decimal('listed_percentage', 5, 2)->default(0)->after('allow_bank_financing');
        });
    }

    public function down(): void
    {
        Schema::table('rf_communities', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'product_code', 'license_number',
                'license_issue_date', 'license_expiry_date', 'completion_percent',
                'allow_cash_sale', 'allow_bank_financing', 'listed_percentage',
            ]);
        });
    }
};
