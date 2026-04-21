<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Unit;
use Database\Seeders\StatusSeeder;
use Database\Seeders\UnitCategorySeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UnitTypeSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class Phase7SeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unit_seeder_assigns_each_seeded_owner_to_a_unit(): void
    {
        $this->seed([
            StatusSeeder::class,
            UnitCategorySeeder::class,
            UnitTypeSeeder::class,
        ]);

        $this->seed(UnitSeeder::class);

        $this->assertGreaterThanOrEqual(5, Owner::query()->count());
        $this->assertGreaterThanOrEqual(15, Unit::query()->count());
        $this->assertSame(0, Owner::query()->doesntHave('units')->count());
    }
}
