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
        Schema::create('rf_invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->decimal('vat', 5, 2)->default(0);
            $table->text('instructions')->nullable();
            $table->text('notes')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('cr_number')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_invoice_settings');
    }
};
