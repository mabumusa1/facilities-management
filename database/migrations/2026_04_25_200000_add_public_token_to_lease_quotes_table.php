<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_quotes', function (Blueprint $table): void {
            // UUID token for public/prospect-facing preview link (no auth required).
            $table->uuid('public_token')->nullable()->unique()->after('quote_number');
        });
    }

    public function down(): void
    {
        Schema::table('lease_quotes', function (Blueprint $table): void {
            $table->dropColumn('public_token');
        });
    }
};
