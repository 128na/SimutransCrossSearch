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

  public function tweet($message = 'hello')
  {
    return $this->fetch('POST', 'statuses/update', ['status' => $message]);
  }

  public function reply($message, $user_name, $tweet_id)
  {
    $message = "@{$user_name} {$message}";
    return $this->fetch('POST', 'statuses/update', ['status' => $message, 'in_reply_to_status_id' => $tweet_id]);
  }

  private function fetch($method, $action, $params = [])
  {
    return $this->client->{$method}($action, $params);
  }

  public static function cleanText($text)
  {
    return trim(str_replace(['@'.env('TWITTER_USER')], '', $text));
  }

  public static function cleanId($text)
  {
    preg_match("/\/(\d+)$/", $text, $matches);
    return (string)trim($matches[1] ?? null);
  }
}
