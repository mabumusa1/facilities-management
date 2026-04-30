<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('lease_renewal_offers');

        // Drop any orphaned composite type left by a previously failed migration (PostgreSQL-specific).
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP TYPE IF EXISTS lease_renewal_offers CASCADE');
        }

        Schema::create('lease_renewal_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->date('new_start_date');
            $table->unsignedInteger('duration_months');
            $table->decimal('new_rent_amount', 12, 2);
            $table->string('payment_frequency')->nullable();
            $table->foreignId('contract_type_id')->nullable()->constrained('rf_settings');
            $table->date('valid_until');
            $table->text('message_en')->nullable();
            $table->text('message_ar')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->foreignId('converted_lease_id')->nullable()->constrained('rf_leases');
            $table->foreignId('account_tenant_id')->nullable()->constrained('tenants');
            $table->timestamps();

            $table->index('lease_id');
            $table->index('account_tenant_id');
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_renewal_offers');
    }
};
