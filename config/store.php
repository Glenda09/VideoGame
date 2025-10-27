<?php

return [
    'vat_rate' => (float) env('VAT_RATE', 0.19),
    'currency' => env('STORE_CURRENCY', 'USD'),
    'support_email' => env('STORE_SUPPORT_EMAIL', 'support@gamestore.test'),
];
