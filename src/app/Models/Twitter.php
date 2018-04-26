<?php
namespace App\Models;

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
  private $client;

  public function __construct()
  {
    $this->client = new TwitterOAuth(
      env('CONSUMER_KEY'),
      env('CONSUMER_SECRET'),
      env('ACCESS_TOKEN'),
      env('ACCESS_TOKEN_SECRET')
    );
  }

  public function getMentionsWithSinceId($since_id = 1)
  {
    $mentions = collect($this->fetch('GET', 'statuses/mentions_timeline', [
      'since_id' => $since_id
    ]));

    return $mentions->map(function($mention) {
      return [
        'text' => static::cleanText($mention->text),
        'from' => $mention->user->screen_name,
      ];
    });
  }

  public function tweet($message = 'hello')
  {
    return $this->fetch('POST', 'statuses/update', ['status' => $message]);
  }

  private function fetch($method, $action, $params = [])
  {
    return $this->client->{$method}($action, $params);
  }

  public static function cleanText($text)
  {
    return trim(str_replace(['@'.env('TWITTER_USER')], '', $text));
  }
}
