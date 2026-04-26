<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    private const OTP_LENGTH = 6;

    private const OTP_TTL_MINUTES = 10;

    private const MAX_ATTEMPTS = 5;

    public function generate(string $identifier): string
    {
        $otp = (string) random_int(100000, 999999);
        $attemptKey = "otp:{$identifier}:attempts";

        Cache::put("otp:{$identifier}", $otp, now()->addMinutes(self::OTP_TTL_MINUTES));
        Cache::put($attemptKey, 0, now()->addMinutes(self::OTP_TTL_MINUTES));

        return $otp;
    }

    public function verify(string $identifier, string $submittedOtp): bool
    {
        $otpKey = "otp:{$identifier}";
        $attemptKey = "otp:{$identifier}:attempts";

        $storedOtp = Cache::get($otpKey);

        if (! $storedOtp) {
            return false;
        }

        $attempts = Cache::get($attemptKey, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::forget($otpKey);
            Cache::forget($attemptKey);

            return false;
        }

        Cache::increment($attemptKey);

        if ((string) $storedOtp === (string) $submittedOtp) {
            Cache::forget($otpKey);
            Cache::forget($attemptKey);

            return true;
        }

        return false;
    }

    public function resend(string $identifier): string
    {
        return $this->generate($identifier);
    }
}
