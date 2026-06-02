<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ForcePasswordChange
 *
 * Redirects College Admin users to the password change screen
 * if their must_change_password flag is set to true.
 * They cannot access any other page until they change their password.
 */
class ForcePasswordChange
{
    // Routes that are always accessible even when password change is required
    private const ALLOWED_ROUTES = [
        'password.change.show',
        'password.change.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Super admins are never forced to change password via this middleware
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if password change is required
        if ($user->must_change_password) {
            $currentRoute = $request->route()?->getName();

            // Allow the password change routes and logout
            if (in_array($currentRoute, self::ALLOWED_ROUTES)) {
                return $next($request);
            }

            // Redirect to password change page
            return redirect()->route('password.change.show')
                ->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}
