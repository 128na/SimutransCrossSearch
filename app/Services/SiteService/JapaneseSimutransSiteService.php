<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use App\Services\SiteService\Exceptions\ElementNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use LogicException;
use Symfony\Component\DomCrawler\Crawler;

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
        $url = $this->url.'?cmd=list';

        $crawler = $this->client->request('GET', $url);
        $urls = collect($crawler->filter('#body > ul li')->each(function ($node) {
            return $node->filter('a')->attr('href');
        }));

        return $this->modifyUrls($urls);
    }

    private function modifyUrls($urls)
    {
        // EUC-JPなのでUTF-8とは変換が異なる
        $str_pak = '%A5%A2%A5%C9%A5%AA%A5%F3%2F'; // アドオン/
        $str_pak128 = 'Addon128%2F'; // Addon128/
        $str_pak128jp = 'Addon128Japan%2F'; // Addon128Japan/
        $str_report = '%CA%F3%B9%F0'; // 報告

        return $urls
            ->filter(function ($url) use ($str_pak, $str_pak128, $str_pak128jp) { // アドオンページ以外を除去
            return stripos($url, $str_pak) !== false
                    || stripos($url, $str_pak128) !== false
                    || stripos($url, $str_pak128jp) !== false;
            })
            ->filter(function ($url) { // 見出しを除去
            return stripos($url, $this->url) !== false;
            })
            ->filter(function ($url) use ($str_report) { // 不要ページを除去
            return stripos($url, 'menubar') === false
                    && stripos($url, 'Train%20Index') === false
                    && stripos($url, 'TrainIndexNew') === false
                    && stripos($url, $str_report) === false;
            });
    }

    public function isUpdated(RawPage $raw_page, string $html): bool
    {
        $crawler = $this->getCrawler($html);
        try {
            $last_modified = $this->extractLastModified($crawler);

            return $last_modified >= $raw_page->updated_at;
        } catch (LogicException $e) {
            logger()->channel('failed_html')->error($e->getMessage());
            logger()->error($raw_page->url);

            return false;
        }
    }

    private function extractLastModified(Crawler $crawler): Carbon
    {
        $el = $crawler->filter('div#lastmodified');
        if ($el->count()) {
            $text = $el->text();
            $text = str_replace('Last-modified:', '', $text);
            $text = trim($text);
            $text = str_replace([' (月)', ' (火)', ' (水)', ' (木)', ' (金)', ' (土)', ' (日)'], '', $text);

            return Carbon::createFromFormat('Y-m-d H:i:s', $text);
        }
        throw new ElementNotFoundException($crawler->html());
    }

    public function extractContents(RawPage $raw_page): array
    {
        $crawler = $this->getCrawler($raw_page->html);

        $title = $this->extractTitle($crawler);
        $text = $this->extractText($crawler);
        $paks = $this->extractPaks($raw_page->url);
        $last_modified = $this->extractLastModified($crawler);

        return compact('title', 'text', 'paks', 'last_modified');
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
        // EUC-JPなのでUTF-8とは変換が異なる
        $str_pak = '%A5%A2%A5%C9%A5%AA%A5%F3%2F'; // アドオン/
        $str_pak128 = 'Addon128%2F'; // Addon128/
        $str_pak128jp = 'Addon128Japan%2F'; // Addon128Japan/

        if (stripos($url, $str_pak) !== false) {
            return ['64'];
        }
        if (stripos($url, $str_pak128) !== false) {
            return ['128'];
        }
        if (stripos($url, $str_pak128jp) !== false) {
            return ['128-japan'];
        }

        return [];
    }
}
