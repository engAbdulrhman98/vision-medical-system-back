<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'عذراً، يجب تسجيل الدخول للوصول لهذه العملية.',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Super Admin / Admin / CEO role bypass
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Admin', 'CEO'])) {
            return $next($request);
        }

        if (empty($permissions)) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        $hasPermission = false;

        foreach ($permissions as $perm) {
            // Check via Spatie Permission package
            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($perm)) {
                $hasPermission = true;
                break;
            }

            // Check via Laravel built-in Gate/can
            if (method_exists($user, 'can') && $user->can($perm)) {
                $hasPermission = true;
                break;
            }

            // Fallback check in user permissions array
            if (isset($user->permissions) && is_array($user->permissions) && (in_array('*', $user->permissions) || in_array($perm, $user->permissions))) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            return response()->json([
                'status' => false,
                'message' => 'عذراً، ليس لديك الصلاحية الكافية لإتمام هذه العملية.',
                'required_permissions' => $permissions
            ], 403);
        }

        return $next($request);
    }
}
