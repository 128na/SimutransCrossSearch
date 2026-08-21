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
    ) {}

    /**
     * @return Collection<int,string>
     */
    public function __invoke(): Collection
    {
        $urls = $this->getTargetUrls()
            ->map(fn (string $url): string => $this->toFullUrl($url))
            ->filter(fn (string $url): bool => $this->filter($url));

        // 一覧ページの構造が変わるなどして 0 件になった場合、例外を投げずに
        // 静かに終わると誰にも気づかれないまま(過去に約21ヶ月放置された)。
        // ScrapeAction/ScrapeCommand の既存の失敗検知・通知経路に乗せる。
        if ($urls->isEmpty()) {
            throw new \RuntimeException('Japan: FindUrls matched 0 target URLs. サイトの一覧ページ構造が変わった可能性があります。');
        }

        return $urls;
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

        return collect($urls)->filter(fn ($url): bool => is_string($url));
    }

    /**
     * サイト側が相対URL・非エンコードのスラッシュを返すようになったため、
     * href が相対/絶対・新旧いずれのエンコーディングでも同じ絶対URLに
     * 正規化する（既存 raw_pages.url との一致を保ち、重複行を作らない）。
     * Twitrans\FindUrls::toFullUrl() とは異なり、単純な相対→絶対の連結
     * ではなく旧来のパーセントエンコード形式への再構築を行う。
     */
    private function toFullUrl(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (is_string($host) && strtolower($host) !== 'japanese.simutrans.com') {
            // 自サイト以外の href（誤って一覧に混入した外部リンク等）は対象外。
            return $url;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        if (! is_string($query) || $query === '') {
            return $url;
        }

        return self::TOP_URL.':443/index.php?'.rawurlencode(urldecode($query));
    }

    private function filter(string $url): bool
    {
        $url = strtolower($url);
        // アドオンページ以外
        if (
            ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?addon128%2f')  // Addon128/
            && ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?addon128japan%2f')  // Addon128/Japan/
            && ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?addons%2f64%2f')  // Addons/
            && ! str_starts_with($url, 'https://japanese.simutrans.com:443/index.php?%a5%a2%a5%c9%a5%aa%a5%f3%2f')  // アドオン/
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
