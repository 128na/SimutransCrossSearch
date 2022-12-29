<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Carbon\Carbon;
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
        $url = $this->url.'?cmd=list';

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
                && stripos($url, 'index') === false
                && stripos($url, 'menubar') === false
                && stripos($url, $str_copy) === false;
            })
            ->map(function ($url) use ($root) { // 相対URLを絶対URLに加工
            return str_replace("/{$root}", $this->url, $url);
            });
    }

    public function isUpdated(RawPage $raw_page, string $html): bool
    {
        $crawler = $this->getCrawler($html);
        $last_modified = $this->extractLastModified($crawler);

        return $last_modified >= $raw_page->updated_at;
    }

    private function extractLastModified($crawler): Carbon
    {
        $text = $crawler->filter('div#lastmodified')->text();
        $text = str_replace('Last-modified:', '', $text);
        $text = trim($text);
        $text = str_replace([' (月)', ' (火)', ' (水)', ' (木)', ' (金)', ' (土)', ' (日)'], '', $text);

        return Carbon::createFromFormat('Y-m-d H:i:s', $text);
    }

    public function extractContents(RawPage $raw_page): array
    {
        $html = $this->modifyHTML($raw_page->html);
        $crawler = $this->getCrawler($html);

        $title = $this->extractTitle($crawler);
        $text = $this->extractText($crawler);
        $paks = $this->extractPaks($raw_page->url);
        $last_modified = $this->extractLastModified($crawler);

        return compact('title', 'text', 'paks', 'last_modified');
    }

    /**
     * scriptタグを除去する
     */
    private function modifyHTML($html)
    {
        return preg_replace('/<script([\s\S]+?)script>/mi', '', $html);
    }

    private function extractTitle($crawler)
    {
        $title = $crawler->filter('title')->text();

        return str_replace(' - Simutrans的な実験室 Wiki*', '', $title);
    }

    private function extractText($crawler)
    {
        return $crawler->filter('div#content')->text();
    }

    private function extractPaks($url)
    {
        if (stripos($url, 'pak64/') !== false) {
            return ['64'];
        }
        if (stripos($url, 'pak128/') !== false) {
            return ['128'];
        }
        if (stripos($url, 'pak128.japan/') !== false) {
            return ['128-japan'];
        }

        return [];
    }
}
