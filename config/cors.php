<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:5173', // Vite default port
        'http://127.0.0.1:5173',
        // this is vercel link
        'https://expense-tracker-kappa-ashen-90.vercel.app',
    ],

    'allowed_origins_patterns' => [
        '#^https://.+--expense-tracker-kappa-ashen-90\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
