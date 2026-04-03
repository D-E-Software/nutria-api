<?php

$defaultOrigins = [
    'http://localhost:3000',
    'http://admin.localhost:3000',
    'https://bewell-two.vercel.app',
    'https://bewellklinik.com',
    'https://www.bewellklinik.com',
    'https://admin.bewellklinik.com',
];

$configuredOrigins = array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))));

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $configuredOrigins ?: $defaultOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
