<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds invoice-numbering, payment-terms, and bilingual branding fields to
     * rf_invoice_settings. Existing columns (company_name, logo, address, vat,
     * instructions, notes, vat_number, cr_number) are preserved for legacy compat.
     *
     * IMPORTANT: invoice_next_sequence must only be incremented atomically in
     * Accounting code using DB::table('rf_invoice_settings')->lockForUpdate()
     * inside a transaction. Never read-then-write without a row-level lock.
     * See Accounting stories #194 / #227 for the consuming pattern.
     */
    public function up(): void
    {
        Schema::table('rf_invoice_settings', function (Blueprint $table) {
            // Bilingual branding (legacy company_name/logo kept nullable for compat)
            $table->string('name_en')->nullable()->after('company_name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('logo_path')->nullable()->after('logo');
            $table->string('logo_ar_path')->nullable()->after('logo_path');

            // Appearance
            $table->string('timezone', 64)->default('UTC')->after('logo_ar_path');
            $table->string('primary_color', 7)->nullable()->after('timezone');

            // Invoice numbering — must be incremented with lockForUpdate() in Accounting
            $table->string('invoice_prefix', 20)->default('INV')->after('primary_color');
            $table->unsignedBigInteger('invoice_next_sequence')->default(1)->after('invoice_prefix');

            // Payment terms
            $table->unsignedSmallInteger('payment_terms_days')->default(30)->after('invoice_next_sequence');
            $table->decimal('late_payment_penalty_pct', 5, 2)->nullable()->after('payment_terms_days');
            $table->unsignedSmallInteger('late_payment_grace_days')->default(0)->after('late_payment_penalty_pct');

            // Invoice footer (bilingual)
            $table->text('footer_text_en')->nullable()->after('late_payment_grace_days');
            $table->text('footer_text_ar')->nullable()->after('footer_text_en');

            // VAT display toggle
            $table->boolean('show_vat_number')->default(true)->after('footer_text_ar');

            // Enforce one-row-per-tenant at DB level
            $table->unique('account_tenant_id', 'rf_invoice_settings_account_tenant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_invoice_settings', function (Blueprint $table) {
            $table->dropUnique('rf_invoice_settings_account_tenant_unique');
            $table->dropColumn([
                'name_en',
                'name_ar',
                'logo_path',
                'logo_ar_path',
                'timezone',
                'primary_color',
                'invoice_prefix',
                'invoice_next_sequence',
                'payment_terms_days',
                'late_payment_penalty_pct',
                'late_payment_grace_days',
                'footer_text_en',
                'footer_text_ar',
                'show_vat_number',
            ]);
        });
    }
};
