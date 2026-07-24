<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiActivityMiddleware
{
    /**
     * Log mutating API actions (POST, PUT, PATCH, DELETE) to activity_log table.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log mutating HTTP methods on successful responses (200-299)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $user = $request->user();
            if ($user && function_exists('activity')) {
                try {
                    $actionName = strtolower($request->method()) . ' ' . $request->path();
                    activity()
                        ->causedBy($user)
                        ->withProperties([
                            'ip' => $request->ip(),
                            'user_agent' => substr((string) $request->userAgent(), 0, 255),
                        ])
                        ->log($actionName);
                } catch (\Throwable $e) {
                    // Silently ignore logging errors to prevent breaking API response
                }
            }
        }

        return $response;
    }
}
