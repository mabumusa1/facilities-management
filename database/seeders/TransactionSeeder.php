<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(LeaseSeeder::class);

        $targetCount = 10;
        $missingCount = $targetCount - Transaction::query()->count();

        if ($missingCount <= 0) {
            return;
        }

        $category = Setting::query()->firstOrCreate(
            [
                'type' => 'transaction_category',
                'name_en' => 'Rent',
            ],
            [
                'name' => 'Rent',
                'name_ar' => 'إيجار',
                'parent_id' => null,
            ],
        );

        $type = Setting::query()->firstOrCreate(
            [
                'type' => 'transaction_type',
                'name_en' => 'Invoice',
            ],
            [
                'name' => 'Invoice',
                'name_ar' => 'فاتورة',
                'parent_id' => null,
            ],
        );

        $statusIds = Status::query()->where('type', 'invoice')->pluck('id');
        $leaseIds = Lease::query()->pluck('id');

        if ($statusIds->isEmpty() || $leaseIds->isEmpty()) {
            return;
        }

        for ($index = 0; $index < $missingCount; $index++) {
            $amount = random_int(2000, 25000);
            $isPaid = random_int(0, 1) === 1;

            Transaction::factory()->create([
                'lease_id' => $leaseIds->random(),
                'category_id' => $category->id,
                'type_id' => $type->id,
                'status_id' => $statusIds->random(),
                'amount' => $amount,
                'tax_amount' => round($amount * 0.15, 2),
                'rental_amount' => $amount,
                'due_on' => now()->addDays(random_int(-30, 30))->toDateString(),
                'vat' => 15,
                'is_paid' => $isPaid,
            ]);
        }
    }
}
