<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class FindUrls
{
    private const DOMAIN = 'https://wikiwiki.jp';

    private const LIST_URL = 'https://wikiwiki.jp/twitrans?cmd=list';

    public function __construct(
        private readonly FetchHtml $fetchHtml,
    ) {
    }

    /**
     * @return Collection<int,string>
     */
    public function __invoke(): Collection
    {
        return $this->getTargetUrls()
            ->filter(fn ($url): bool => $this->filter($url))
            ->map(fn ($url): string => $this->toFullUrl($url));
    }

    /**
     * @return Collection<int,string>
     */
    private function getTargetUrls(): Collection
    {
        $response = ($this->fetchHtml)(self::LIST_URL, 'UTF-8');
        $urls = $response
            ->filter('#content>ul li')
            ->each(fn (Crawler $crawler): ?string => $crawler->filter('a')->attr('href'));

        return collect($urls);
    }

    private function filter(string $url): bool
    {
        $url = strtolower($url);
        // アドオンページ以外
        if (! str_contains($url, 'addon/pak64/') && ! str_contains($url, 'addon/pak128/') && ! str_contains($url, 'addon/pak128.japan/')) {
            return false;
        }

        // 不要ページ
        return ! (str_contains($url, 'test') || str_contains($url, 'index') || str_contains($url, 'menubar') || str_contains($url, '%e8%a4%87%e8%a3%bd'));
    }

    private function toFullUrl(string $url): string
    {
        return self::DOMAIN.$url;
    }
}
