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
        Schema::create('rf_visitor_logs', function (Blueprint $table) {
            $table->id();

            // Tenant scope — BelongsToAccountTenant trait adds global scope on this column
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();

            // Nullable FK — null means this is a walk-in (no prior invitation)
            $table->unsignedBigInteger('invitation_id')->nullable();
            $table->foreign('invitation_id')->references('id')->on('rf_visitor_invitations');

            $table->unsignedBigInteger('community_id');
            $table->foreign('community_id')->references('id')->on('rf_communities');

            $table->string('visitor_name', 255);
            $table->string('visitor_phone', 50)->nullable();
            $table->string('purpose', 50)->nullable();

            // Gate officer who processed the entry/exit
            $table->unsignedBigInteger('gate_officer_id');
            $table->foreign('gate_officer_id')->references('id')->on('users');

            $table->dateTime('entry_at');
            $table->dateTime('exit_at')->nullable();

            $table->boolean('id_verified')->default(false);
            $table->string('photo_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_visitor_logs');
    }
};
