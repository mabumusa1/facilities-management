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
        Schema::create('rf_visitor_invitations', function (Blueprint $table) {
            $table->id();

            // Tenant scope — BelongsToAccountTenant trait adds global scope on this column
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();

            $table->unsignedBigInteger('community_id');
            $table->foreign('community_id')->references('id')->on('rf_communities');

            // Resident who created the invitation
            $table->unsignedBigInteger('resident_id');
            $table->foreign('resident_id')->references('id')->on('users');

            $table->string('visitor_name', 255);
            $table->string('visitor_phone', 50)->nullable();

            /**
             * Enum values: visit, delivery, service, other
             */
            $table->string('visitor_purpose', 50)->default('visit');

            $table->dateTime('expected_at');
            $table->dateTime('valid_until');

            /**
             * Enum values: pending, active, used, expired, cancelled
             */
            $table->string('status', 50)->default('pending');

            $table->text('notes')->nullable();

            // QR code token — opaque DB lookup (not JWT), unique per invitation
            $table->uuid('qr_code_token')->unique();

            /**
             * Enum values: none, link
             */
            $table->string('qr_code_sent_via', 50)->default('none');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_visitor_invitations');
    }
};
