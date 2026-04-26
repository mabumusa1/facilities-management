<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('type_id')->nullable()->change();
            $table->unsignedBigInteger('status_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Reverting not null constraints is destructive — skip
    }
};
