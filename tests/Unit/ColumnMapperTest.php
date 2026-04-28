<?php

namespace Tests\Unit;

use App\Services\ColumnMapper;
use PHPUnit\Framework\TestCase;

class ColumnMapperTest extends TestCase
{
    // ─── guess() ─────────────────────────────────────────────────────────────

    public function test_guess_matches_unit_name_variants(): void
    {
        $this->assertSame('name', ColumnMapper::guess('Unit Name'));
        $this->assertSame('name', ColumnMapper::guess('Unit Number'));
        $this->assertSame('name', ColumnMapper::guess('Unit No'));
        $this->assertSame('name', ColumnMapper::guess('unit-name'));
        $this->assertSame('name', ColumnMapper::guess('unit_no'));
    }

    public function test_guess_matches_community(): void
    {
        $this->assertSame('rf_community_id', ColumnMapper::guess('Community'));
        $this->assertSame('rf_community_id', ColumnMapper::guess('Community Name'));
        $this->assertSame('rf_community_id', ColumnMapper::guess('Project'));
    }

    public function test_guess_matches_building(): void
    {
        $this->assertSame('rf_building_id', ColumnMapper::guess('Building'));
        $this->assertSame('rf_building_id', ColumnMapper::guess('Building Name'));
        $this->assertSame('rf_building_id', ColumnMapper::guess('Bldg'));
    }

    public function test_guess_matches_area(): void
    {
        $this->assertSame('net_area', ColumnMapper::guess('Area'));
        $this->assertSame('net_area', ColumnMapper::guess('Net Area'));
        $this->assertSame('net_area', ColumnMapper::guess('Area (sqm)'));
    }

    public function test_guess_matches_status(): void
    {
        $this->assertSame('status', ColumnMapper::guess('Status'));
        $this->assertSame('status', ColumnMapper::guess('Unit Status'));
    }

    public function test_guess_returns_null_for_unknown_header(): void
    {
        $this->assertNull(ColumnMapper::guess('Foo Bar'));
        $this->assertNull(ColumnMapper::guess('Random Column'));
        $this->assertNull(ColumnMapper::guess(''));
    }

    // ─── autoMatch() ─────────────────────────────────────────────────────────

    public function test_auto_match_maps_standard_headers(): void
    {
        $headers = ['Unit Name', 'Community', 'Building', 'Area (sqm)', 'Status'];
        $result = ColumnMapper::autoMatch($headers);

        $this->assertSame('Unit Name', $result['name']);
        $this->assertSame('Community', $result['rf_community_id']);
        $this->assertSame('Building', $result['rf_building_id']);
        $this->assertSame('Area (sqm)', $result['net_area']);
        $this->assertSame('Status', $result['status']);
    }

    public function test_auto_match_returns_null_for_unmatched_system_fields(): void
    {
        $headers = ['Unit Name', 'Community'];
        $result = ColumnMapper::autoMatch($headers);

        $this->assertSame('Unit Name', $result['name']);
        $this->assertSame('Community', $result['rf_community_id']);
        $this->assertNull($result['rf_building_id']);
        $this->assertNull($result['net_area']);
        $this->assertNull($result['status']);
    }

    public function test_auto_match_handles_empty_headers(): void
    {
        $result = ColumnMapper::autoMatch([]);

        foreach ($result as $field => $value) {
            $this->assertNull($value, "Expected null for field {$field}");
        }
    }

    public function test_system_fields_returns_all_known_fields(): void
    {
        $fields = ColumnMapper::systemFields();

        $this->assertContains('name', $fields);
        $this->assertContains('rf_community_id', $fields);
        $this->assertContains('rf_building_id', $fields);
        $this->assertContains('net_area', $fields);
        $this->assertContains('status', $fields);
    }
}
