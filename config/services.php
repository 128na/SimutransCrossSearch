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
    'discord' => [
        'token' => env('DISCORD_TOKEN'),
        'client_id' => env('DISCORD_CLIENT_ID'),
        'trigger' => '!cs',
        'timeout' => 300,
    ],
    'line' => [
        'channel_secret' => env('LINE_CHANNEL_SECRET'),
        'channel_access_token' => env('LINE_CHANNEL_ACCESS_TOKEN'),
    ],
    'notion' => [
        'secret' => env('NOTION_SECRET'),
        'database_id' => env('NOTION_DATABASE_ID'),
    ],
];
