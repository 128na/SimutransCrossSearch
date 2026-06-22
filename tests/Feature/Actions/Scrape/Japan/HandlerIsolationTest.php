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
 * A1: 1 URL の失敗で他の URL の処理が止まらないこと（scrape 側）。
 */
final class HandlerIsolationTest extends TestCase
{
    public function test_failure_on_one_url_does_not_stop_the_others(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="https://japanese.simutrans.com:443/index.php?Addon128%2FBroken">broken</a></li>'
            .'<li><a href="https://japanese.simutrans.com:443/index.php?Addon128%2FOk">ok</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
            'https://japanese.simutrans.com/index.php?Addon128%2FBroken' => fn (): never => throw new ConnectionException('connection failed'),
            'https://japanese.simutrans.com/index.php?Addon128%2FOk' => Http::response('<html><body>ok</body></html>', 200),
        ]);

        $handler = new Handler(
            new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false),
            new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false)),
            new UpdateOrCreateRawPage,
        );

        $handler(new NullLogger);

        // 失敗した URL は書き込まれず、成功した URL は書き込まれること。
        $this->assertSame(1, RawPage::query()->count());
        $this->assertDatabaseHas('raw_pages', ['url' => 'https://japanese.simutrans.com:443/index.php?Addon128%2FOk']);
        $this->assertDatabaseMissing('raw_pages', ['url' => 'https://japanese.simutrans.com:443/index.php?Addon128%2FBroken']);
    }
}
