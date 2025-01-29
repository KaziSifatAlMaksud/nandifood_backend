<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add global CORS headers middleware
        $middleware->push(function ($request, $next) {
            $response = $next($request);

            // Set the CORS headers to allow all origins (you can replace '*' with a specific domain)
            $response->headers->set('Access-Control-Allow-Origin', '*'); // or 'https://yourdomain.com'
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

            // Handle preflight requests (OPTIONS method)
            if ($request->getMethod() == 'OPTIONS') {
                return response()->json([], 200);
            }

            return $response;
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
