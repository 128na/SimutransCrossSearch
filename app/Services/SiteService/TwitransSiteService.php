<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Illuminate\Support\Collection;

class TwitransSiteService extends SiteService
{
    public function __construct(RawPage $raw_page, Page $page, Pak $pak)
    {
        parent::__construct(
            config('sites.twitrans'),
            $raw_page,
            $page,
            $pak
        );
    }

    public function getUrls(): Collection
    {
        $url = $this->url . '?cmd=list';

        $crawler = $this->client->request('GET', $url);
        $urls = collect($crawler->filter('#content>ul li')->each(function ($node) {
            return $node->filter('a')->attr('href');
        }));

        return $this->modifyUrls($urls);
    }

    private function modifyUrls($urls)
    {
        $root = basename($this->url);
        $str_copy = urlencode('複製');

        return $urls
            ->filter(function ($url) { // アドオンページ以外を除去
                return stripos($url, 'addon/pak64/') !== false
                || stripos($url, 'addon/pak128/') !== false
                || stripos($url, 'addon/pak128.japan/') !== false;
            })
            ->filter(function ($url) use ($root) { // 見出しを削除
                return stripos($url, $root) !== false;
            })
            ->filter(function ($url) use ($str_copy) { // 不要ページを除去
                return stripos($url, 'test') === false
                && stripos($url, 'companyIndex') === false
                && stripos($url, 'menubar') === false
                && stripos($url, $str_copy) === false;
            })
            ->map(function ($url) use ($root) { // 相対URLを絶対URLに加工
                return str_replace("/{$root}", $this->url, $url);
            });
    }

    public function extractContents(RawPage $raw_page): array
    {
        $crawler = $this->client->request('GET', $url);

        return [
            'title' => '',
            'text' => '',
        ];
    }
}
