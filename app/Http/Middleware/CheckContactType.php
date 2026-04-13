<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\ContactType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if the authenticated user has one of the allowed contact types.
 */
class CheckContactType
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$types  Allowed contact types (owner, tenant, admin, professional)
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        // Convert string types to ContactType enum values
        $allowedTypes = array_filter(
            array_map(fn ($type) => ContactType::tryFrom($type), $types)
        );

        if (empty($allowedTypes)) {
            // If no valid types provided, deny access
            abort(403, 'No valid contact types specified.');
        }

        // Check if user's contact type is in the allowed list
        foreach ($allowedTypes as $allowedType) {
            if ($user->isContactType($allowedType)) {
                return $next($request);
            }
        }

        abort(403, 'Your user type does not have access to this resource.');
    }
}
