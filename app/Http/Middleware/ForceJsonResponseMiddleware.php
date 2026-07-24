<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ForceJsonResponseMiddleware
{
    /**
     * Handle an incoming request and force JSON response headers,
     * skipping binary downloads and export endpoints.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isExportRequest = $request->is('*/export*') || $request->is('api/*/export');

        if (!$isExportRequest) {
            $request->headers->set('Accept', 'application/json');
        }

        $response = $next($request);

        if (
            method_exists($response, 'headers') &&
            !$isExportRequest &&
            !$response instanceof BinaryFileResponse &&
            !$response instanceof StreamedResponse
        ) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
