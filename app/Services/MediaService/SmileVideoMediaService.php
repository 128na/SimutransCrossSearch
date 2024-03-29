<?php

namespace App\Services\MediaService;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://site.nicovideo.jp/search-api-docs/search.html
 */
class SmileVideoMediaService extends MediaService
{
    protected HttpClientInterface $client;

    public function __construct(Article $article)
    {
        parent::__construct(config('media.nico'), $article);

        $this->client = HttpClient::create(['timeout' => 60]);
    }

    public function search(string $word, $limit = 50): Collection
    {
        $query = [
            'q' => $word,
            'targets' => 'title,description,tags',
            'fields' => 'contentId,title,description,thumbnailUrl,startTime',
            '_sort' => '-startTime',
            '_limit' => $limit,
            '_context' => config('app.name'),
        ];

        return $this->fetch($query);
    }

    public function searchOld(string $word, Carbon $date, $limit = 50): Collection
    {
        $query = [
            'q' => $word,
            'targets' => 'title,description,tags',
            'fields' => 'contentId,title,description,thumbnailUrl,startTime',
            // 2015-01-01T00:00:00+09:00
            'filters[startTime][lt]' => $date->toAtomString(),
            '_sort' => '-startTime',
            '_limit' => $limit,
            '_context' => config('app.name'),
        ];

        return $this->fetch($query);
    }

    private function fetch(array $query)
    {
        $end_point = 'https://api.search.nicovideo.jp/api/v2/snapshot/video/contents/search';
        $query = http_build_query($query);
        $url = "{$end_point}?{$query}";

        $res = $this->client->request('GET', $url);
        $content = $res->toArray();

        return collect($content['data'])->map(function ($item) {
            return [
                'title' => $item['title'],
                'text' => $item['description'] ?? '',
                'media_type' => 'video',
                'url' => "{$this->url}/watch/{$item['contentId']}",
                'thumbnail_url' => $item['thumbnailUrl'].'.L',
                // 2020-01-02T03:04:05+09:00
                'last_modified' => Carbon::create($item['startTime'])->tz(config('app.timezone')),
            ];
        });
    }
}
