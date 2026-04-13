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
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_ar', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default transaction types
        DB::table('transaction_types')->insert([
            ['id' => 1, 'name' => 'Paid', 'name_ar' => 'مدفوع', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Due', 'name_ar' => 'مستحقة', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
    }
};
