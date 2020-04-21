<?php

return [
    'portal' => [
        'name' => 'portal',
        'display_name' => 'Simutrans Addon Portal',
        'url' => 'http://localhost:1080',
        // 'url' => 'https://simutrans.sakura.ne.jp/portal',
        'token' => env('SIMUTRANS_PORTAL_TOKEN'),
    ],
    'japan' => [
        'name' => 'japan',
        'display_name' => 'Simutrans Japan',
        'url' => 'https://japanese.simutrans.com',
    ],
    'twitrans' => [
        'name' => 'twitrans',
        'display_name' => 'Simutrans的な実験室',
        'url' => 'https://wikiwiki.jp/twitrans',
    ],
];
