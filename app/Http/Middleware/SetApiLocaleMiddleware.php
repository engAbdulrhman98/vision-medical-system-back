<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocaleMiddleware
{
    /**
     * Set the application locale based on incoming headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->header('X-Locale') 
            ?? $request->get('lang', 'ar');

        if (in_array(strtolower($locale), ['ar', 'en'])) {
            App::setLocale(strtolower($locale));
        } else {
            App::setLocale('ar');
        }

        return $next($request);
    }
}
