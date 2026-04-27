<?php

namespace App\Actions\Settings;

use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class ResolveSessionInfo
{
    protected Agent $agent;

    public function __construct()
    {
        $this->agent = new Agent;
    }

    public function resolve(string $userAgent, ?string $ipAddress): array
    {
        $this->agent->setUserAgent($userAgent);

        return [
            'agent' => [
                'browser' => $this->agent->browser() ?: 'Unknown',
                'platform' => $this->agent->platform() ?: 'Unknown',
                'device' => $this->agent->device() ?: ($this->agent->isMobile() ? 'Mobile' : 'Desktop'),
            ],
            'location' => $this->resolveLocation($ipAddress),
        ];
    }

    protected function resolveLocation(?string $ipAddress): ?string
    {
        if ($ipAddress === null || $ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return null;
        }

        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->get("http://ip-api.com/json/{$ipAddress}?fields=city,country");

            if ($response->successful()) {
                $data = $response->json();
                $parts = array_filter([$data['city'] ?? null, $data['country'] ?? null]);

                return $parts !== [] ? implode(', ', $parts) : null;
            }
        } catch (\Throwable) {
            // Geolocation failure is non-critical — return null
        }

        return null;
    }
}
