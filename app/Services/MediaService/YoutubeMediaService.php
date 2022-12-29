<?php

namespace App\Services\MediaService;

use App\Models\Article;
use Carbon\Carbon;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\Collection;

/**
 * @see https://developers.google.com/youtube/v3/getting-started?hl=ja
 */
class YoutubeMediaService extends MediaService
{
    private Google_Service_YouTube $client;

    public function __construct(Article $article)
    {
        parent::__construct(config('media.youtube'), $article);

        $client = new Google_Client();
        $client->setDeveloperKey(config('media.youtube.key'));

        $this->client = new Google_Service_YouTube($client);
    }

    public function search(string $word, $limit = 50): Collection
    {
        $query = [
            'q' => $word,
            'order' => 'date',
            'type' => 'video',
            'maxResults' => $limit,
        ];

        return $this->fetch($query);
    }

    public function searchOld(string $word, Carbon $date, $limit = 50): Collection
    {
        $query = [
            'q' => $word,
            'order' => 'date',
            'type' => 'video',
            'publishedBefore' => $date->toRfc3339String(),
            'maxResults' => $limit,
        ];

        return $this->fetch($query);
    }

    private function fetch(array $query)
    {
        $res = $this->client->search->listSearch('id,snippet', $query);

        $videos = collect([]);
        foreach ($res['items'] as $item) {
            if ($item['id']['kind'] === 'youtube#video') {
                $videos->push([
                    'title' => $item['snippet']['title'],
                    'text' => $item['snippet']['description'] ?? '',
                    'media_type' => 'video',
                    'url' => "{$this->url}/watch?v={$item['id']['videoId']}",
                    'thumbnail_url' => $item['snippet']['thumbnails']['high']['url'],
                    // 2020-01-02T03:04:05.000Z
                    'last_modified' => Carbon::create($item['snippet']['publishedAt'])->tz(config('app.timezone')),
                ]);
            }
        }

        return $videos;
    }
}
