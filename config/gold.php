<?php

return [
    'api_url' => env('IRANGOLD_API_URL', 'https://api.irangold.app/api/v1/pricing'),
    'token' => env('IRANGOLD_API_TOKEN'),
    'verify_ssl' => env('IRANGOLD_VERIFY_SSL', true),
];
