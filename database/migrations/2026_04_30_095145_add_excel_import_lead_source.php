<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('rf_lead_sources')->upsert(
            [
                [
                    'id' => 12,
                    'name' => 'Excel Import',
                    'name_en' => 'Excel Import',
                    'name_ar' => 'استيراد Excel',
                ],
            ],
            ['id'],
            ['name', 'name_en', 'name_ar'],
        );
    }

    public function down(): void
    {
        DB::table('rf_lead_sources')->where('id', 12)->delete();
    }
};
