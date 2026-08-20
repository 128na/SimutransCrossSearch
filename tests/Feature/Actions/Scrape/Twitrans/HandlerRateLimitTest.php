<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\Twitrans\FindUrls;
use App\Actions\Scrape\Twitrans\Handler;
use App\Actions\Scrape\UpdateOrCreateRawPage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

final class HandlerRateLimitTest extends TestCase
{
    public function test_backs_off_longer_after_429_than_after_success(): void
    {
        Http::preventStrayRequests();
        Sleep::fake();

        $listHtml = '<html><body><div id="content"><ul>'
            .'<li><a href="/twitrans/addon/pak128.japan/Ok">ok</a></li>'
            .'<li><a href="/twitrans/addon/pak128.japan/Limited">limited</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://wikiwiki.jp/twitrans?cmd=list' => Http::response($listHtml, 200),
            'https://wikiwiki.jp/twitrans/addon/pak128.japan/Ok' => Http::response('<html><body>ok</body></html>', 200),
            'https://wikiwiki.jp/twitrans/addon/pak128.japan/Limited' => Http::response('rate limited', 429),
        ]);

        $handler = new Handler(
            new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false),
            new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false)),
            new UpdateOrCreateRawPage,
        );

        $handler(new NullLogger);

        // 成功時は通常間隔(2秒)、429 を受けた直後はより長いクールダウン(15秒)。
        Sleep::assertSequence([
            Sleep::for(2)->seconds(),
            Sleep::for(15)->seconds(),
        ]);
    }
}
