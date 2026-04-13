<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$permissions  Permission strings in format "action-subject" or "action:subject"
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        foreach ($permissions as $permission) {
            // Check if using action:subject format
            if (str_contains($permission, ':')) {
                [$actionStr, $subjectStr] = explode(':', $permission, 2);
                $action = PermissionAction::tryFrom($actionStr);
                $subject = PermissionSubject::tryFrom($subjectStr);

                if ($action && $subject) {
                    $permission = $subject->permissionFor($action);
                }
            }

            if (! $user->hasPermissionTo($permission)) {
                abort(403, 'Unauthorized. Missing permission: '.$permission);
            }
        }

        return $next($request);
    }
}
