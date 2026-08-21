<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape\Japan;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\Japan\FindUrls;
use Illuminate\Support\Facades\Http;
use Tests\Feature\TestCase;

final class FindUrlsTest extends TestCase
{
    public function test_normalizes_relative_unencoded_hrefs_to_the_legacy_absolute_url(): void
    {
        Http::preventStrayRequests();

        // サイト側が相対URL・非エンコードのスラッシュを返すようになった現在の実際の形式。
        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="./?Addon128/Aircrafts">Aircrafts</a></li>'
            .'<li><a href="./?Addon128/Aircrafts+2">Aircrafts 2</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://japanese.simutrans.com:443/index.php?Addon128%2FAircrafts',
            'https://japanese.simutrans.com:443/index.php?Addon128%2FAircrafts%202',
        ], $urls->values()->all());
    }

    public function test_still_matches_the_legacy_absolute_encoded_href(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="https://japanese.simutrans.com:443/index.php?Addon128%2FAircrafts">Aircrafts</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://japanese.simutrans.com:443/index.php?Addon128%2FAircrafts',
        ], $urls->values()->all());
    }

    public function test_excludes_non_addon_pages(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="./?RecentChanges">RecentChanges</a></li>'
            .'<li><a href="./?Addon128">Addon128</a></li>'
            .'<li><a href="./?Addon128/MenuBar">MenuBar</a></li>'
            .'<li><a href="./?Addon128/Aircrafts">Aircrafts</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://japanese.simutrans.com:443/index.php?Addon128%2FAircrafts',
        ], $urls->values()->all());
    }

    public function test_includes_the_standalone_addon_namespace_with_encoded_japanese_page_name(): void
    {
        Http::preventStrayRequests();

        // 「アドオン/」名前空間（EUC-JPパーセントエンコード、スラッシュのみ非エンコード）。
        // filter() の :443 抜けコピペミスの回帰テスト。
        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="./?%A5%A2%A5%C9%A5%AA%A5%F3/%CE%F3%BC%D620">addon</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://japanese.simutrans.com:443/index.php?%A5%A2%A5%C9%A5%AA%A5%F3%2F%CE%F3%BC%D620',
        ], $urls->values()->all());
    }

    public function test_ignores_hrefs_from_a_different_host(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="https://example.com/?Addon128/Evil">evil</a></li>'
            .'<li><a href="./?Addon128/Aircrafts">Aircrafts</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://japanese.simutrans.com:443/index.php?Addon128%2FAircrafts',
        ], $urls->values()->all());
    }

    public function test_throws_when_no_urls_match(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="body"><ul>'
            .'<li><a href="./?RecentChanges">RecentChanges</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://japanese.simutrans.com?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $this->expectException(\RuntimeException::class);

        ($findUrls)();
    }
}
