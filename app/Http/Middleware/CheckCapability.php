<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCapability
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$capabilities  Required capabilities based on ManagerRole
     */
    public function handle(Request $request, Closure $next, string ...$capabilities): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        foreach ($capabilities as $capability) {
            if (! $user->hasCapability($capability)) {
                abort(403, 'Unauthorized. Missing capability: '.$capability);
            }
        }

        return $next($request);
    }
}
