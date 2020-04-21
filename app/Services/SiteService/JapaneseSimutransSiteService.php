<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Illuminate\Support\Collection;

class JapaneseSimutransSiteService extends SiteService
{
    public function __construct(RawPage $raw_page, Page $page, Pak $pak)
    {
        parent::__construct(
            config('sites.japan'),
            $raw_page,
            $page,
            $pak
        );
    }

    public function getUrls(): Collection
    {
        $url = $this->url . '?cmd=list';

        $crawler = $this->client->request('GET', $url);
        $urls = collect($crawler->filter('#body > ul li')->each(function ($node) {
            return $node->filter('a')->attr('href');
        }));

        return $this->modifyUrls($urls);
    }

    private function modifyUrls($urls)
    {
        $str_pak = urlencode('アドオン/');
        $str_pak128 = urlencode('Addon128/');
        $str_pak128jp = urlencode('Addon128Japan/');

        return $urls
            ->filter(function ($url) use ($str_pak, $str_pak128, $str_pak128jp) { // アドオンページ以外を除去
                return stripos($url, $str_pak) !== false
                || stripos($url, $str_pak) !== false
                || stripos($url, $str_pak) !== false;
            })
            ->filter(function ($url) { // 見出しを除去
                return stripos($url, $this->url) !== false;
            });
    }

    public function extractContents(RawPage $raw_page): array
    {
        $crawler = $this->getCrawler($raw_page);

        $title = $this->extractTitle($crawler);
        $text = $this->extractText($crawler);
        $paks = $this->extractPaks($raw_page->url);

        return compact('title', 'text', 'paks');
    }

    private function extractTitle($crawler)
    {
        $title = $crawler->filter('title')->text();
        return str_replace(' - Simutrans日本語化･解説', '', $title);
    }

    private function extractText($crawler)
    {
        return $crawler->filter('div#body')->text();
    }

    private function extractPaks($url)
    {
        if (stripos($url, 'アドオン/')) {
            return ['64'];
        }
        if (stripos($url, 'Addon128/')) {
            return ['128'];
        }
        if (stripos($url, 'Addon128Japan/')) {
            return ['128-japan'];
        }
        return [];
    }

}
