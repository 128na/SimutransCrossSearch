<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape\Japan;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\Japan\FindUrls;
use App\Actions\Scrape\Japan\Handler;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use App\Models\RawPage;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

/**
 * A2: 取得失敗時に RawPage を空/部分データで書き込まない。
 * fetch が例外を投げた URL は upsert に到達せず、行が作られないこと。
 */
final class HandlerFailureTest extends TestCase
{
    public function test_does_not_write_raw_page_when_fetch_fails(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="body"><ul><li>'
            .'<a href="https://japanese.simutrans.com:443/index.php?Addon128%2FTest">test</a>'
            .'</li></ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
            // PSR-7 URI は文字列化時に既定ポート(443)を省略するため、
            // Http::fake のキーは href の表記（:443 付き）ではなく実際の送信先 URL に合わせる。
            'https://japanese.simutrans.com/index.php?Addon128%2FTest' => fn (): never => throw new ConnectionException('connection failed'),
        ]);

        $handler = new Handler(
            new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false),
            new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false)),
            new UpdateOrCreateRawPage,
        );

        $handler(new NullLogger);

        $this->assertSame(0, RawPage::query()->count());
    }
}
