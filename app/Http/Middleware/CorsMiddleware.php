<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Define the CORS headers
        $headers = [
            'Access-Control-Allow-Origin' => '*',  // Allow all origins, can replace '*' with specific domains (e.g., 'https://example.com')
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS', // Allowed methods
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With', // Allowed headers
            'Access-Control-Allow-Credentials' => 'true', // Allow credentials like cookies
        ];

        // Handle preflight OPTIONS requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200, $headers);
        }

        // Continue with the request and add CORS headers to the response
        $response = $next($request);
        return $response->withHeaders($headers);
    }
}
