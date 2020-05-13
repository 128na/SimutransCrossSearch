<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;

class TweetService
{
    private TwitterOAuth $client;

    public function __construct()
    {
        $this->client = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            config('twitter.access_token'),
            config('twitter.access_token_secret')
        );
    }

    public function postMedia($media_paths = [], $message = '')
    {
        $media_paths = collect($media_paths);

        // 通知を$notifiableインスタンスへ送信する…
        if (\App::environment(['production'])) {
            $media = $this->uploadMedia($media_paths);
            $params = [
                'status' => $message,
                'media_ids' => $media->pluck('media_id_string')->implode(','),
            ];
            $this->handleResponse($this->client->post('statuses/update', $params));
        }
        logger(sprintf('Tweet with media %s, %s', $message, $media_paths->implode(', ')));
    }

    private function uploadMedia($media_paths)
    {
        return $media_paths->map(function ($media_path) {
            return $this->handleResponse($this->client->upload('media/upload', ['media' => $media_path]));
        });
    }

    private function handleResponse($res)
    {
        if (isset($res->errors)) {
            $msg = 'Tweet failed';
            foreach ($res->errors as $error) {
                $msg .= $error->message;
            }
            throw new \Exception($msg, 1);
        }
        return $res;
    }
}
