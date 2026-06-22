<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape;

use App\Actions\Scrape\FetchHtml;
use App\Enums\Encoding;
use Illuminate\Http\Client\RequestException;
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

    public function test_throws_on_non_2xx_response_instead_of_returning_error_body(): void
    {
        $url = 'https://example.com/not-found';

        Http::fake([
            $url => Http::response('<html><body>404 Not Found</body></html>', 404),
        ]);

        $fetchHtml = new FetchHtml(
            retryTimes: 1,
            sleepMilliseconds: 1,
            useCache: false
        );

        $this->expectException(RequestException::class);
        $fetchHtml($url, Encoding::UTF_8);
    }

    public function test_does_not_retry_on_4xx_client_error(): void
    {
        $url = 'https://example.com/forbidden';

        Http::fake([
            $url => Http::response('forbidden', 403),
        ]);

        $fetchHtml = new FetchHtml(
            retryTimes: 3,
            sleepMilliseconds: 1,
            useCache: false
        );

        try {
            $fetchHtml($url, Encoding::UTF_8);
            $this->fail('exception expected');
        } catch (RequestException) {
            // 4xx は解消しないため即失敗し、リトライしない。
        }

        Http::assertSentCount(1);
    }

    public function test_retries_on_5xx_server_error(): void
    {
        $url = 'https://example.com/server-error';

        Http::fake([
            $url => Http::response('server error', 500),
        ]);

        $fetchHtml = new FetchHtml(
            retryTimes: 3,
            sleepMilliseconds: 1,
            useCache: false
        );

        try {
            $fetchHtml($url, Encoding::UTF_8);
            $this->fail('exception expected');
        } catch (RequestException) {
            // 5xx は一過性の可能性があるため retryTimes 回まで再試行する。
        }

        Http::assertSentCount(3);
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
