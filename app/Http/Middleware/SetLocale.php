<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale($this->resolveLocale($request));

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $headerLocale = $this->normalizeLocale($request->header('X-App-Locale') ?? $request->header('X-Locale'));

        if ($headerLocale !== null) {
            return $headerLocale;
        }

        $cookieLocale = $this->normalizeLocale($request->cookie('locale'));

        if ($cookieLocale !== null) {
            return $cookieLocale;
        }

        $preferredLocale = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);

        if ($preferredLocale !== null) {
            return $preferredLocale;
        }

        $appLocale = config('app.locale');

        return $this->normalizeLocale(is_string($appLocale) ? $appLocale : null) ?? 'en';
    }

    private function normalizeLocale(?string $locale): ?string
    {
        if (! is_string($locale) || $locale === '') {
            return null;
        }

        $normalized = strtolower(trim(explode(',', $locale)[0]));
        $normalized = str_replace('_', '-', $normalized);
        $normalized = explode('-', $normalized)[0] ?? '';

        if (! in_array($normalized, self::SUPPORTED_LOCALES, true)) {
            return null;
        }

        return $normalized;
    }
}
