<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 *
 * Restricts route access based on user role.
 * Usage: middleware('role:college_admin,staff')
 *
 * Special handling:
 * - 'super_admin' in the allowed list matches isSuperAdmin() flag
 * - Super admin does NOT bypass all role checks anymore —
 *   they are only allowed where 'super_admin' is explicitly listed.
 *   This prevents super admin from accidentally accessing student routes.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if 'super_admin' is in the allowed roles list
        if (in_array('super_admin', $roles) && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Super admin is NOT allowed on student-only routes
        // (prevents super admin from accidentally seeing student portal)
        if ($user->isSuperAdmin() && !in_array('super_admin', $roles)) {
            // Super admin trying to access a non-super-admin route
            // Allow if it's an admin route (college_admin is in the list)
            if (in_array('college_admin', $roles)) {
                return $next($request);
            }
            // Block super admin from student-only routes
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied for this role.'], 403);
            }
            return redirect()->route('dashboard');
        }

        // Check if user has one of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized. Insufficient permissions.'], 403);
        }

        abort(403, 'Access denied. You do not have permission to view this page.');
    }
}
