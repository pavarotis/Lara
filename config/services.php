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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Fonts API
    |--------------------------------------------------------------------------
    |
    | API key for Google Fonts API (optional but recommended).
    |
    | ✅ COST: FREE - No billing required, no credit card needed
    | ✅ RATE LIMITS: Very generous (with cache, you'll never hit them)
    |
    | Get your API key from: https://console.cloud.google.com/apis/credentials
    |
    | Steps:
    | 1. Go to Google Cloud Console
    | 2. Create a new project or select existing
    | 3. Enable "Web Fonts Developer API"
    | 4. Create credentials (API Key)
    | 5. Add the key to your .env file as GOOGLE_FONTS_API_KEY
    |
    | Note: The API works without a key but may have rate limits.
    |       With API key: Better rate limits, more reliable
    |       Without API key: Still works, but may have restrictions
    |
    */

    'google_fonts' => [
        'api_key' => env('GOOGLE_FONTS_API_KEY'),
    ],

];
