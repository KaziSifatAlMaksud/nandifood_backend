<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your CORS settings for your application. By default,
    | Laravel uses the fruitcake/laravel-cors package, and the default configuration
    | allows you to configure your CORS settings easily.
    |
    */
    
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Allow CORS for API routes and Sanctum cookie route

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP Methods
    |--------------------------------------------------------------------------
    |
    | Here you can specify which HTTP methods are allowed when accessing your
    | application via an API. By default, we allow all HTTP methods.
    |
    */
    'allowed_methods' => ['*'], // Allow all methods (GET, POST, PUT, DELETE, OPTIONS)

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | This allows you to specify which origins are allowed to send requests.
    | The `*` wildcard allows all origins. You can specify individual origins
    | as well like `['https://example.com']` if you want to restrict.
    |
    */
    'allowed_origins' => ['*'], // Allow all origins

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | If you want to allow any origin with a specific pattern, you can use this.
    | For example, `^https?://.*\.example\.com$` will allow all subdomains of example.com.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP Headers
    |--------------------------------------------------------------------------
    |
    | Here you can specify which HTTP headers are allowed when accessing your
    | application via an API. You can specify `*` to allow all headers.
    |
    */
    'allowed_headers' => ['*'], // Allow all headers

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Here you can specify which headers should be exposed to the browser.
    | This is useful when you need to access custom headers from the client side.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | This option determines how long the results of a pre-flight request can
    | be cached by the browser. You can adjust it to optimize performance.
    |
    */
    'max_age' => 0, // Cache pre-flight requests for 0 seconds

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | This option determines if cookies or HTTP authentication will be sent
    | with the requests. Set this to `true` if you need to support credentials.
    |
    */
    'supports_credentials' => false, // Set to true if using cookies/sessions with your API
];
