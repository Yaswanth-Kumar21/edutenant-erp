<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TenantMiddleware
 *
 * Resolves and binds the current tenant to every request.
 * Ensures authenticated users can only access their own tenant's data.
 * Super admins bypass tenant isolation.
 */
class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super admin has access to all tenants — no isolation needed
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Resolve tenant from authenticated user first
        if ($user && $user->tenant_id) {
            $tenant = $user->tenant;

            if (!$tenant || !$tenant->isActive()) {
                auth()->logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your institution account is inactive or suspended.']);
            }

            TenantService::setTenant($tenant);
            return $next($request);
        }

        // If user is logged in but has no tenant (and not super admin), deny
        if ($user) {
            return $next($request);
        }

        // Fallback: resolve from request (domain/subdomain)
        $tenant = TenantService::resolveFromRequest($request);

        if (!$tenant) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Tenant not found.'], 404);
            }
            abort(404, 'Institution not found.');
        }

        TenantService::setTenant($tenant);
        return $next($request);
    }
}
