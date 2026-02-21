<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', config('house_keys.google.client_id')),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', config('house_keys.google.client_secret')),
        'redirect' => env('GOOGLE_REDIRECT', config('house_keys.google.redirect')),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY', config('house_keys.google.maps_key')),
    ],

    'lenco' => [
        'base_url' => env('LENCO_BASE_URL', config('house_keys.lenco.base_url', 'https://api.lenco.co/access/v2')),
        'key' => env('LENCO_KEY', config('house_keys.lenco.key')),
        'secret' => env('LENCO_SECRET', config('house_keys.lenco.secret')),
        'webhook_secret' => env('LENCO_WEBHOOK_SECRET', config('house_keys.lenco.webhook_secret')),
    ],

];
