<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape;

use App\Actions\Scrape\FetchHtml;
use App\Enums\Encoding;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Feature\TestCase;

final class FetchHtmlTest extends TestCase
{
    public function test_invokes_http_and_returns_crawler(): void
    {
        $url = 'https://example.com/test';
        $htmlContent = '<html><body><h1>Test Content</h1><script>alert(1);</script></body></html>';

        Http::fake([
            $url => Http::response($htmlContent, 200),
        ]);

        $fetchHtml = new FetchHtml(
            retryTimes: 1,
            sleepMilliseconds: 10,
            useCache: false
        );

        $crawler = $fetchHtml($url, Encoding::UTF_8);

        $this->assertInstanceOf(Crawler::class, $crawler);
        $this->assertStringContainsString('Test Content', $crawler->html());

        Http::assertSentCount(1);
    }

    public function test_uses_cache_when_enabled(): void
    {
        $url = 'https://example.com/cached';
        $cachedHtml = '<html><body>Cached Content</body></html>';

        Cache::put('url:'.$url, $cachedHtml);

        Http::fake();

        $fetchHtml = new FetchHtml(
            retryTimes: 1,
            sleepMilliseconds: 10,
            useCache: true
        );

        $crawler = $fetchHtml($url, Encoding::UTF_8);

        $this->assertInstanceOf(Crawler::class, $crawler);
        $this->assertStringContainsString('Cached Content', $crawler->html());

        Http::assertNothingSent();
    }

    public function test_converts_encoding(): void
    {
        $url = 'https://example.com/euc-jp';
        // HTML content encoded in EUC-JP
        $eucJpHtml = mb_convert_encoding('<html><body>日本語</body></html>', 'EUC-JP', 'UTF-8');

        Http::fake([
            $url => Http::response($eucJpHtml, 200),
        ]);

        $fetchHtml = new FetchHtml(
            retryTimes: 1,
            sleepMilliseconds: 10,
            useCache: false
        );

        $crawler = $fetchHtml($url, Encoding::EUC_JP);

        $this->assertInstanceOf(Crawler::class, $crawler);
        // After conversion, it should be readable as UTF-8
        $this->assertStringContainsString('日本語', $crawler->html());
    }
}
