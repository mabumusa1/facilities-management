<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensure the authenticated user is verified.
 *
 * For professionals, checks that they have a manager_type assigned.
 * Redirects unverified users to the verification page.
 */
class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if professional needs verification
        if ($user->isProfessional()) {
            // Professional without manager type should go to no-access
            if ($user->manager_role === null && $user->service_manager_type === null) {
                return redirect()->route('no-access');
            }
        }

        return $next($request);
    }
}
