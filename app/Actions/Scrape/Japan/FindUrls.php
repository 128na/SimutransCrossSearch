<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Japan;

use App\Actions\Scrape\FetchHtml;
use App\Enums\Encoding;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

final readonly class FindUrls
{
    private const string TOP_URL = 'https://japanese.simutrans.com';

    private const string LIST_URL = 'https://japanese.simutrans.com?cmd=list';

    public function __construct(
        private FetchHtml $fetchHtml,
    ) {
    }

    /**
     * @return Collection<int,string>
     */
    public function __invoke(): Collection
    {
        return $this->getTargetUrls()
            ->filter(fn ($url): bool => $this->filter($url));
    }

    /**
     * @return Collection<int,string>
     */
    private function getTargetUrls(): Collection
    {
        $response = ($this->fetchHtml)(self::LIST_URL, Encoding::EUC_JP);
        $urls = $response
            ->filter('#body > ul li')
            ->each(fn (Crawler $crawler): ?string => $crawler->filter('a')->attr('href'));

        return collect($urls);
    }

    private function filter(string $url): bool
    {
        $url = strtolower($url);
        // アドオンページ以外
        if (
            ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?addon128%2f')  // Addon128/
            && ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?addon128japan%2f')  // Addon128/Japan/
            && ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?addons%2f64%2f')  // Addons/
            && ! str_starts_with($url, 'https://japanese.simutrans.com/index.php?%a5%a2%a5%c9%a5%aa%a5%f3%2f')  // アドオン/
        ) {
            return false;
        }

        // 目次など
        if (! str_contains($url, self::TOP_URL)) {
            return false;
        }

        // 不要ページ
        return ! (str_contains($url, 'menubar')
        || str_contains($url, 'header')
        || str_contains($url, '%ca%f3%b9%f0'));
    }
}
