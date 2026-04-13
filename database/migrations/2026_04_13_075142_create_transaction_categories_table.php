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
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_ar', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default categories
        DB::table('transaction_categories')->insert([
            ['id' => 1, 'name' => 'Rentals', 'name_ar' => 'الإيجارات', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => 'Insurance Refund', 'name_ar' => 'استرجاع التأمين', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_categories');
    }
};
