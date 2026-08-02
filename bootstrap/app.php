<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global API Middlewares
        $middleware->api(append: [
            \App\Http\Middleware\ForceJsonResponseMiddleware::class,
            \App\Http\Middleware\SetApiLocaleMiddleware::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        // Named Middleware Aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRoleMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermissionMiddleware::class,
            'force.json' => \App\Http\Middleware\ForceJsonResponseMiddleware::class,
            'api.locale' => \App\Http\Middleware\SetApiLocaleMiddleware::class,
            'api.activity' => \App\Http\Middleware\LogApiActivityMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && !($e instanceof \Illuminate\Validation\ValidationException)) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile().':'.$e->getLine(),
                ], 500);
            }
        });
    })->create();
