<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Add the necessary CORS headers
        $headers = [
            'Access-Control-Allow-Origin' => '*', // Or set a specific domain
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200, $headers);
        }

        // Continue with the request and add CORS headers
        $response = $next($request);
        return $response->withHeaders($headers);
    }
}
