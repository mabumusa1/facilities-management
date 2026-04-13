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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('transaction_categories');
            $table->foreignId('subcategory_id')->nullable()->constrained('transaction_subcategories');
            $table->foreignId('type_id')->constrained('transaction_types');
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('unit_id')->nullable()->constrained();
            $table->unsignedBigInteger('lease_id')->nullable(); // Will be FK when Lease model is created
            $table->foreignId('assignee_id')->constrained('contacts');

            // Amount fields
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('rental_amount', 15, 2)->default(0);
            $table->decimal('additional_fees_amount', 15, 2)->default(0);
            $table->decimal('vat', 5, 2)->default(0);
            $table->decimal('paid', 15, 2)->default(0);
            $table->decimal('left', 15, 2)->default(0);

            // Reference fields
            $table->string('lease_number')->nullable();
            $table->text('details')->nullable();
            $table->json('additional_fees')->nullable();
            $table->json('images')->nullable();

            // Dates
            $table->date('due_on')->nullable();

            // Status flags
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_old')->default(false);
            $table->boolean('assignee_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('due_on');
            $table->index('is_paid');
            $table->index(['tenant_id', 'status_id']);
            $table->index(['tenant_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
