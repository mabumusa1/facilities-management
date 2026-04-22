<?php

namespace Database\Seeders;

use App\Support\DefaultSubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DefaultSubscriptionPlan::ensure();
    }
}
