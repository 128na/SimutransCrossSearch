<?php

return [
  'app' => [
    'name'        => 'Simutransアドオン横断検索',
    'description' => 'Simutransのアドオンを横断検索できます。',
    'keywords'    => ['Simutrans', 'Addon', 'シムトランス', 'アドオン', 'pak', 'pak128', 'pak128.japan'],
    'author'      => '128Na',
    'type'        => 'website',
    'twittercard' => 'summary',
    'image'       => '/icon.png',
    'favicon'     => '/favicon.ico',
    'rss_cache'   => 'framework/cache/rss',
    'rss_expired' => 6 * 60 * 60,

  ],
  'sites' => [
    'JapaneseSimutrans' => ['name' => 'Simutrans Japan',   'url' => 'http://japanese.simutrans.com'],
    'Twitrans'          => ['name' => 'Simutrans的な実験室', 'url' => 'http://wikiwiki.jp/twitrans'],
    'SimutransPortal'   => ['name' => 'Simutransポータル',    'url' => 'https://simutrans.sakura.ne.jp/portal'],
  ],
  'pak' => [
    '64'       => '01',
    '128'      => '02',
    '128japan' => '03',

  ],
  'rss' => [
    ['name' => 'JapaneseSimutrans', 'url' => 'http://japanese.simutrans.com/index.php?cmd=rss'],
    ['name' => 'Twitrans',          'url' => 'http://wikiwiki.jp/twitrans/?cmd=mixirss'],
    ['name' => 'SimutransPortal',   'url' => 'https://simutrans.sakura.ne.jp/portal/feed'],
  ],
  'twitter' => [
    'name' => '@128Na',
    'url'  => 'https://twitter.com/128Na',
  ],
  'github' => [
    'url' => 'https://github.com/128na/SimutransCrossSearch',
  ],
  'cron' => [
    ['name' => 'JapaneseSimutrans', 'time' => '1:00'],
    ['name' => 'Twitrans',          'time' => '2:00'],
    ['name' => 'SimutransPortal',   'time' => '3:00'],
  ],

];
