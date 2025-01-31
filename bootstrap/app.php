<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CorsMiddleware;
use Illuminatech\MultipartMiddleware\MultipartFormDataParser;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(CorsMiddleware::class);
        // $middleware->append(MultipartFormDataParser::class); 
        // $middleware->append(\App\Http\Middleware\CheckForMaintenanceMode::class);
        // $middleware->append(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
        // $middleware->append(\App\Http\Middleware\TrimStrings::class);
        // $middleware->append(\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle exceptions if needed
    })
    ->create();
