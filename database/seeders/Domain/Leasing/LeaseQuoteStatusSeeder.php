<?php

namespace Database\Seeders\Domain\Leasing;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds rf_statuses rows for type=lease_quote.
 *
 * IDs 70–75 are reserved for the lease-quote status machine:
 *   draft → sent → viewed → accepted | rejected | expired
 *
 * Run standalone:  php artisan db:seed --class="Database\Seeders\Domain\Leasing\LeaseQuoteStatusSeeder"
 */
class LeaseQuoteStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'id' => 70,
                'name' => 'draft',
                'name_en' => 'draft',
                'name_ar' => 'مسودة',
                'priority' => 1,
                'type' => 'lease_quote',
            ],
            [
                'id' => 71,
                'name' => 'sent',
                'name_en' => 'sent',
                'name_ar' => 'تم الإرسال',
                'priority' => 2,
                'type' => 'lease_quote',
            ],
            [
                'id' => 72,
                'name' => 'viewed',
                'name_en' => 'viewed',
                'name_ar' => 'تمت المشاهدة',
                'priority' => 3,
                'type' => 'lease_quote',
            ],
            [
                'id' => 73,
                'name' => 'accepted',
                'name_en' => 'accepted',
                'name_ar' => 'مقبول',
                'priority' => 4,
                'type' => 'lease_quote',
            ],
            [
                'id' => 74,
                'name' => 'rejected',
                'name_en' => 'rejected',
                'name_ar' => 'مرفوض',
                'priority' => 5,
                'type' => 'lease_quote',
            ],
            [
                'id' => 75,
                'name' => 'expired',
                'name_en' => 'expired',
                'name_ar' => 'منتهي الصلاحية',
                'priority' => 6,
                'type' => 'lease_quote',
            ],
        ];

        DB::table('rf_statuses')->upsert(
            $statuses,
            ['id'],
            ['name', 'name_en', 'name_ar', 'priority', 'type'],
        );
    }
}
