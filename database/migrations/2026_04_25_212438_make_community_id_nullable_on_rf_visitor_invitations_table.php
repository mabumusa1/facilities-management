<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make community_id nullable on rf_visitor_invitations.
     *
     * The resident portal creates invitations without a known community context —
     * community can be resolved later via the resident's lease if needed.
     */
    public function up(): void
    {
        Schema::table('rf_visitor_invitations', function (Blueprint $table) {
            $table->unsignedBigInteger('community_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rf_visitor_invitations', function (Blueprint $table) {
            $table->unsignedBigInteger('community_id')->nullable(false)->change();
        });
    }
};
