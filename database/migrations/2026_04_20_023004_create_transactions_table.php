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
        Schema::create('rf_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->nullable()->constrained('rf_leases')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('rf_units')->nullOnDelete();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->string('assignee_type')->nullable();
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('rental_amount', 12, 2)->nullable();
            $table->decimal('additional_fees_amount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->date('due_on')->index();
            $table->text('details')->nullable();
            $table->string('lease_number')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_old')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assignee_type', 'assignee_id']);
            $table->index('lease_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_transactions');
    }
};
