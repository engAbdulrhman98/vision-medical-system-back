<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated access.',
            ], 401);
        }

        // If no specific roles are required, allow access
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the specified roles using Spatie HasRoles or fallback role attribute
        $hasAccess = false;

        if (method_exists($user, 'hasAnyRole')) {
            $hasAccess = $user->hasAnyRole($roles);
        }

        if (!$hasAccess && isset($user->role)) {
            $hasAccess = in_array($user->role, $roles);
        }

        // Super Admin / Admin role bypass check if applicable
        if (!$hasAccess && method_exists($user, 'hasRole') && ($user->hasRole('Admin') || $user->hasRole('CEO'))) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return response()->json([
                'message' => 'Unauthorized action. You do not have permission to access this resource.',
                'required_roles' => $roles,
            ], 403);
        }

        return $next($request);
    }
}
