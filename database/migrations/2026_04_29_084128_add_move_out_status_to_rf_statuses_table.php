<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('rf_statuses')->upsert(
            [
                // Lease status: move-out in progress (leases transition here when move-out is initiated)
                ['id' => 77, 'name' => 'Move-Out In Progress', 'name_en' => 'move_out_in_progress', 'name_ar' => 'إخلاء جاري', 'priority' => 5, 'type' => 'lease'],
                // Move-out record statuses
                ['id' => 78, 'name' => 'In Progress', 'name_en' => 'in_progress', 'name_ar' => 'جاري', 'priority' => 1, 'type' => 'move_out'],
                ['id' => 79, 'name' => 'Completed', 'name_en' => 'completed', 'name_ar' => 'مكتمل', 'priority' => 2, 'type' => 'move_out'],
            ],
            ['id'],
            ['name', 'name_en', 'name_ar', 'priority', 'type'],
        );
    }

    public function down(): void
    {
        DB::table('rf_statuses')->whereIn('id', [77, 78, 79])->delete();
    }
};
