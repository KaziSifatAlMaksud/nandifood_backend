<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Specify paths where CORS should be applied.

    'allowed_methods' => ['*'], // Allow all HTTP methods like GET, POST, PUT, DELETE, etc.
    
    'allowed_origins' => ['*'], // Allow requests from any origin. Replace '*' with specific domains if needed.

    'allowed_origins_patterns' => [], // Patterns for more granular control of allowed origins.

    'allowed_headers' => ['*'], // Allow all headers. You can specify headers like 'Content-Type, Authorization'.

    'exposed_headers' => [], // Headers exposed to the browser. Keep empty unless specific headers are needed.

    'max_age' => 0, // Time in seconds that the browser should cache preflight requests.

    'supports_credentials' => false, // Set to true if cookies or authorization headers are required.
];
