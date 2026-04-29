<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_out_deductions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('move_out_id')->constrained('move_outs')->cascadeOnDelete();
            $table->string('label_en');
            $table->string('label_ar');
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_out_deductions');
    }
};
