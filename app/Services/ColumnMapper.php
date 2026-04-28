<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Fuzzy-matches Excel column headers to known system field names.
 *
 * System fields for unit import:
 *   name             — Unit Number / Unit Name
 *   rf_community_id  — Community
 *   rf_building_id   — Building
 *   net_area         — Area / Net Area
 *   status           — Status
 */
class ColumnMapper
{
    /**
     * Known aliases for each system field (lowercased slugs).
     *
     * @var array<string, string[]>
     */
    private static array $aliases = [
        'name' => [
            'name', 'unit-name', 'unit-number', 'unit-no', 'unit-num', 'unit',
            'unit-id', 'unitname', 'unitnumber', 'unitno',
        ],
        'rf_community_id' => [
            'community', 'community-name', 'project', 'project-name',
        ],
        'rf_building_id' => [
            'building', 'building-name', 'bldg', 'block',
        ],
        'net_area' => [
            'net-area', 'area', 'area-sqm', 'area-m2', 'net-area-sqm',
            'net-area-m2', 'size', 'size-sqm',
        ],
        'status' => [
            'status', 'unit-status', 'state',
        ],
    ];

    /**
     * Guess a system field name from an Excel column header.
     * Returns null if no match found.
     */
    public static function guess(string $header): ?string
    {
        $slug = Str::slug($header);

        foreach (self::$aliases as $field => $candidateSlugs) {
            if (in_array($slug, $candidateSlugs, true)) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Auto-match a set of Excel headers to system fields.
     *
     * @param  string[]  $headers
     * @return array<string, string|null> keyed by system field name, value is matched header or null
     */
    public static function autoMatch(array $headers): array
    {
        $matched = [];
        $usedHeaders = [];

        foreach (array_keys(self::$aliases) as $field) {
            $matched[$field] = null;
        }

        foreach ($headers as $header) {
            $field = self::guess($header);

            if ($field !== null && ! isset($usedHeaders[$field])) {
                $matched[$field] = $header;
                $usedHeaders[$field] = true;
            }
        }

        return $matched;
    }

    /**
     * Returns the list of known system fields.
     *
     * @return string[]
     */
    public static function systemFields(): array
    {
        return array_keys(self::$aliases);
    }
}
