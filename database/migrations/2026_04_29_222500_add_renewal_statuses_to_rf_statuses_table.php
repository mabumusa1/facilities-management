<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * IDs 83-88 reserved for lease renewal offer state machine (type=renewal).
     */
    public function up(): void
    {
        $statuses = [
            ['id' => 83, 'type' => 'renewal', 'name' => 'draft',    'name_en' => 'Draft',    'name_ar' => 'مسودة',      'priority' => 1],
            ['id' => 84, 'type' => 'renewal', 'name' => 'sent',     'name_en' => 'Sent',     'name_ar' => 'مرسل',       'priority' => 2],
            ['id' => 85, 'type' => 'renewal', 'name' => 'viewed',   'name_en' => 'Viewed',   'name_ar' => 'تم الاطلاع', 'priority' => 3],
            ['id' => 86, 'type' => 'renewal', 'name' => 'accepted', 'name_en' => 'Accepted', 'name_ar' => 'مقبول',      'priority' => 4],
            ['id' => 87, 'type' => 'renewal', 'name' => 'rejected', 'name_en' => 'Rejected', 'name_ar' => 'مرفوض',      'priority' => 5],
            ['id' => 88, 'type' => 'renewal', 'name' => 'expired',  'name_en' => 'Expired',  'name_ar' => 'منتهي',      'priority' => 6],
        ];

        foreach ($statuses as $status) {
            DB::table('rf_statuses')->insertOrIgnore($status);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('rf_statuses')->whereIn('id', [83, 84, 85, 86, 87, 88])->delete();
    }
};
