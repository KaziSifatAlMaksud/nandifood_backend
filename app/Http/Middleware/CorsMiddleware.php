<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        // Handle preflight OPTIONS requests
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json([], 200, $headers);
        }

        // Continue with the request
        $response = $next($request);
           return $response->withHeaders($headers);

        // Ensure the response is an instance of Response before adding headers
        // if ($response instanceof Response) {
        //     foreach ($headers as $key => $value) {
        //         $response->headers->set($key, $value);
        //     }
        // }

        // return $response; // Removed the duplicate return statement
        
    }
}
