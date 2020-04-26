<?php

namespace App\Services\MediaService;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @see http://westplain.sakuraweb.com/translate/twitter/Documentation/REST-APIs/Public-API/GET-search-tweets.cgi
 */
class TwitterMediaService extends MediaService
{
    private $media_types = [
        'video' => 'video',
        'photo' => 'image',
    ];

    /**
     * @var TwitterOAuth
     */
    protected $client;

    public function __construct(Article $article)
    {
        parent::__construct(config('media.twitter'), $article);

        $this->client = new TwitterOAuth(
            config('media.twitter.consumer_key'),
            config('media.twitter.consumer_secret'),
            config('media.twitter.access_token'),
            config('media.twitter.access_token_secret')
        );
    }

    public function search(string $word, $limit = 200): Collection
    {
        $items = collect([]);
        $max_id = null;
        $loop_limit = 50;
        $loop = 0;

        do {
            $items = $items->merge($this->searchReclusive($word, $max_id, 100));
            $max_id = $items->last()['id'] ?? null;
            $loop++;
        } while ($items->count() <= $limit && $loop < $loop_limit && sleep(1) === 0);

        return $items;
    }
    public function searchReclusive(string $word, $max_id = null, $limit = 100): Collection
    {
        $params = [
            'q' => $word,
            'result_type' => 'recent',
            'max_id' => $max_id,
            'count' => $limit,
        ];
        $result = $this->client->get("search/tweets", $params);

        return collect($result->statuses)
            ->filter(function ($item) { // has media?
                return isset($item->extended_entities->media);
            })
            ->filter(function ($item) { // is NOT RT?
                return !isset($item->retweeted_status);
            })
            ->filter(function ($item) { // video or image?
                return collect($item->extended_entities->media)->some(function ($media) {
                    return array_key_exists($media->type, $this->media_types);
                });
            })
            ->map(function ($item) {
                $media = collect($item->extended_entities->media)->first(function ($media) {
                    return array_key_exists($media->type, $this->media_types);
                });

                return [
                    'id' => $item->id,
                    'title' => mb_strimwidth($item->text, 0, 20, '…'),
                    'text' => $item->text,
                    'url' => $media->expanded_url,
                    'media_type' => $this->media_types[$media->type],
                    'thumbnail_url' => $media->media_url_https,
                    'last_modified' => Carbon::createFromFormat('D M d H:i:s +T Y', $item->created_at),
                ];
            })
            ->values();
    }

}
