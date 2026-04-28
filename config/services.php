<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party vendorSetting such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    // // Firebase Admin Dashboard Setup
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
        'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),
        'credentials_json' => env('FIREBASE_CREDENTIALS_JSON'),
        'legacy_server_key' => env('FIREBASE_LEGACY_SERVER_KEY'),
        'timeout' => env('FIREBASE_TIMEOUT', 10),
    ],
    // End Firebase Admin Dashboard Setup

    'myfatoorah' => [
        'mode' => env('MYFATOORAH_MODE', 'test'), // 'test' or 'live'
        'credentials' => [
            'test' => [
                'token' => env('MYFATOORAH_TEST_TOKEN'),
                'base_url' => env('MYFATOORAH_TEST_BASE_URL', 'https://apitest.myfatoorah.com/v3'),
            ],
            'live' => [
                'token' => env('MYFATOORAH_LIVE_TOKEN'),
                'base_url' => env('MYFATOORAH_LIVE_BASE_URL', 'https://api.myfatoorah.com/v3'),
            ],
        ],
    ],
];
