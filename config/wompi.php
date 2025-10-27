<?php

return [
    'public_key' => env('WOMPI_PUBLIC_KEY', ''),
    'private_key' => env('WOMPI_PRIVATE_KEY', ''),
    'acceptance_token' => env('WOMPI_ACCEPTANCE_TOKEN', ''),
    'api_url' => env('WOMPI_API_URL', 'https://sandbox.wompi.co'),
    'events_secret' => env('WOMPI_EVENTS_SECRET', ''),
    'signature_header' => 'X-Wompi-Signature',
    'timeout' => env('WOMPI_TIMEOUT', 10),
    'webhook_route' => 'webhooks.wompi',
    'redirect_urls' => [
        'success' => env('APP_URL', 'http://localhost').'/checkout/success',
        'failure' => env('APP_URL', 'http://localhost').'/checkout/failure',
    ],
];
