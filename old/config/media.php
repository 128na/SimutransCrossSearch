<?php

return [
    'youtube' => [
        'name' => 'youtube',
        'display_name' => 'YouTube',
        'url' => 'https://www.youtube.com',
        'key' => env('GOOGLE_DEVELOPER_KEY'),
    ],
    'nico' => [
        'name' => 'nico',
        'display_name' => 'ニコニコ動画',
        'url' => 'https://www.nicovideo.jp',
    ],
    'twitter' => [
        'name' => 'twitter',
        'display_name' => 'Twitter',
        'url' => 'https://twitter.com',
        'consumer_key' => env('TWITTER_CONSUMER_KEY'),
        'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
    ],
];
