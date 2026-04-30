<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The original move-out status migration (084128) claimed IDs 77-79 which
 * collide with the lease-approval workflow constants in ExpireLeaseQuotes
 * (STATUS_APPROVED_APPLICATION=77, STATUS_REJECTED_APPLICATION=78).
 *
 * This migration:
 *  1. Removes move-out status rows from IDs 77-79.
 *  2. Inserts the correct approval-workflow statuses at IDs 77 and 78.
 *  3. Inserts move-out statuses at the non-colliding IDs 80-82.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: remove the incorrectly-placed move-out rows at 77-79.
        DB::table('rf_statuses')->whereIn('id', [77, 78, 79])->delete();

        // Step 2: add the lease-approval statuses that ExpireLeaseQuotes expects.
        DB::table('rf_statuses')->upsert(
            [
                ['id' => 77, 'name' => 'Approved Application', 'name_en' => 'approved_application', 'name_ar' => 'طلب معتمد', 'priority' => 1, 'type' => 'lease'],
                ['id' => 78, 'name' => 'Rejected Application', 'name_en' => 'rejected_application', 'name_ar' => 'طلب مرفوض', 'priority' => 2, 'type' => 'lease'],
            ],
            ['id'],
            ['name', 'name_en', 'name_ar', 'priority', 'type'],
        );

        // Step 3: insert move-out statuses at non-colliding IDs.
        DB::table('rf_statuses')->upsert(
            [
                ['id' => 80, 'name' => 'Move-Out In Progress', 'name_en' => 'move_out_in_progress', 'name_ar' => 'إخلاء جاري', 'priority' => 5, 'type' => 'lease'],
                ['id' => 81, 'name' => 'In Progress', 'name_en' => 'in_progress', 'name_ar' => 'جاري', 'priority' => 1, 'type' => 'move_out'],
                ['id' => 82, 'name' => 'Completed', 'name_en' => 'completed', 'name_ar' => 'مكتمل', 'priority' => 2, 'type' => 'move_out'],
            ],
            ['id'],
            ['name', 'name_en', 'name_ar', 'priority', 'type'],
        );
    }

    public function down(): void
    {
        // Remove the corrected rows.
        DB::table('rf_statuses')->whereIn('id', [77, 78, 80, 81, 82])->delete();

        // Restore the original (colliding) move-out rows.
        DB::table('rf_statuses')->upsert(
            [
                ['id' => 77, 'name' => 'Move-Out In Progress', 'name_en' => 'move_out_in_progress', 'name_ar' => 'إخلاء جاري', 'priority' => 5, 'type' => 'lease'],
                ['id' => 78, 'name' => 'In Progress', 'name_en' => 'in_progress', 'name_ar' => 'جاري', 'priority' => 1, 'type' => 'move_out'],
                ['id' => 79, 'name' => 'Completed', 'name_en' => 'completed', 'name_ar' => 'مكتمل', 'priority' => 2, 'type' => 'move_out'],
            ],
            ['id'],
            ['name', 'name_en', 'name_ar', 'priority', 'type'],
        );
    }
};
