<?php

namespace App\Support;

class PhoneNumberNormalizer
{
    /**
     * Map of ISO-2 country codes to international dial prefixes used in the
     * primary GCC market. Add codes here as new markets come online.
     *
     * @var array<string, string>
     */
    private const DIAL_CODES = [
        'SA' => '966',
        'AE' => '971',
        'KW' => '965',
        'BH' => '973',
        'QA' => '974',
        'OM' => '968',
    ];

    /**
     * Normalize a phone number to a canonical E.164-style digit string
     * (no `+`, no separators) for tenant-scoped duplicate detection.
     *
     * Returns null when the input does not contain enough digits to be a
     * usable phone number (caller should treat that as "skip the dup check").
     */
    public static function normalize(string $phoneNumber, string $countryCode): ?string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber) ?? '';

        if ($digits === '') {
            return null;
        }

        $digits = ltrim($digits, '0');

        $dial = self::DIAL_CODES[strtoupper($countryCode)] ?? null;

        if ($dial !== null && ! str_starts_with($digits, $dial)) {
            $digits = $dial.$digits;
        }

        return $digits === '' ? null : $digits;
    }
}
