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
        $middleware->push(function ($request, $next) {
            $response = $next($request);

            // Add CORS headers to the response
            $response->headers->set('Access-Control-Allow-Origin', '*'); // Allow all origins, you can specify a domain here
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

            // For preflight requests (OPTIONS), return a response
            if ($request->getMethod() == 'OPTIONS') {
                return response()->json([], 200);
            }

            return $response;
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
