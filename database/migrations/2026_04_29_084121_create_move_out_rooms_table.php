<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_out_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('move_out_id')->constrained('move_outs')->cascadeOnDelete();
            $table->string('name');
            $table->string('condition');
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_out_rooms');
    }
};
