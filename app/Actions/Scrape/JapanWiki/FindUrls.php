<?php

declare(strict_types=1);

namespace App\Actions\Scrape\JapanWiki;

use App\Constants\JapanWikiPaks;
use App\Services\FetchHtml;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class FindUrls
{
    private const TOP_URL = 'https://japanese.simutrans.com';

    // EUC-JPなのでUTF-8とは変換が異なる
    /**
     * アドオン/
     */
    private const PAK = 'https://japanese.simutrans.com/index.php?'.JapanWikiPaks::PAK;

    /**
     * Addon128/
     */
    private const PAK128 = 'https://japanese.simutrans.com/index.php?'.JapanWikiPaks::PAK128;

    /**
     * Addon128Japan/
     */
    private const PAK128JP = 'https://japanese.simutrans.com/index.php?'.JapanWikiPaks::PAK128JP;

    private const LIST_URL = 'https://japanese.simutrans.com?cmd=list';

    /**
     * 報告
     */
    private const REPORT = '%CA%F3%B9%F0';

    public function __construct(
        private readonly FetchHtml $fetchHtml,
    ) {
    }

    /**
     * @return Collection<int,string>
     */
    public function __invoke(): Collection
    {
        return $this->getTargetUrls()->filter(fn ($url): bool => $this->filter($url));
    }

    /**
     * @return Collection<int,string>
     */
    private function getTargetUrls(): Collection
    {
        return collect(
            $this->fetchHtml
                ->request('GET', self::LIST_URL)
                ->filter('#body > ul li')
                ->each(fn (Crawler $crawler): ?string => $crawler->filter('a')->attr('href'))
        )->filter()->values();
    }

    private function filter(string $url): bool
    {
        $url = strtolower($url);
        // アドオンページ以外
        if (! str_starts_with($url, self::PAK) && ! str_starts_with($url, self::PAK128) && ! str_starts_with($url, self::PAK128JP)) {
            return false;
        }

        // 目次など
        if (! str_contains($url, self::TOP_URL)) {
            return false;
        }

        // 不要ページ
        return ! (str_contains($url, 'MenuBar') || str_contains($url, 'header') || str_contains($url, self::REPORT));
    }
}
