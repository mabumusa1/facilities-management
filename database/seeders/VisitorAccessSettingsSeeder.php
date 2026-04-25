<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitorAccessSettingsSeeder extends Seeder
{
    /**
     * Seed one default VisitorAccessSetting row per community.
     *
     * Safe to re-run — upserts on community_id.
     */
    public function run(): void
    {
        $communityRows = DB::table('rf_communities')
            ->select(['id', 'account_tenant_id'])
            ->get();

        $rows = $communityRows->map(fn (object $community) => [
            'account_tenant_id' => $community->account_tenant_id,
            'community_id' => $community->id,
            'require_id_verification' => false,
            'allow_walk_in' => true,
            'qr_expiry_minutes' => 1440,
            'max_uses_per_invitation' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ])->values()->all();

        if (empty($rows)) {
            return;
        }

        DB::table('rf_visitor_access_settings')->upsert(
            $rows,
            ['community_id'],
            ['require_id_verification', 'allow_walk_in', 'qr_expiry_minutes', 'max_uses_per_invitation', 'updated_at']
        );
    }
}
