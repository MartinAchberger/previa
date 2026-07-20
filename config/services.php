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

    'superfaktura' => [
        'email'        => env('SUPERFAKTURA_EMAIL'),
        'api_key'      => env('SUPERFAKTURA_API_KEY'),
        'company_name' => env('SUPERFAKTURA_COMPANY_NAME', 'PREVIA'),
        'company_id'   => (int) env('SUPERFAKTURA_COMPANY_ID', 0),
        'sandbox'      => (bool) env('SUPERFAKTURA_SANDBOX', true),
    ],

    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'foxlog' => [
        'base_url'       => env('FOXLOG_BASE_URL', 'https://app.foxlog.sk/api/v1'),
        'api_token'      => env('FOXLOG_API_TOKEN'),           // outbound auth (our token)
        'webhook_secret' => env('FOXLOG_WEBHOOK_SECRET'),      // inbound auth (Foxlog → us)
        'enabled'        => (bool) env('FOXLOG_ENABLED', false),
    ],

    'packeta' => [
        // Zásielkovňa / Packeta pickup-point widget API key (from the Packeta account).
        'api_key' => env('PACKETA_API_KEY'),
    ],

];
