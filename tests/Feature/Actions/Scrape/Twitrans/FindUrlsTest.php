<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use App\Actions\Scrape\Twitrans\FindUrls;
use Illuminate\Support\Facades\Http;
use Tests\Feature\TestCase;

final class FindUrlsTest extends TestCase
{
    public function test_includes_addon_pages_under_the_three_pak_namespaces(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="content"><ul>'
            .'<li><a href="/twitrans/addon/pak64/train1">train1</a></li>'
            .'<li><a href="/twitrans/addon/pak128/train1">train1</a></li>'
            .'<li><a href="/twitrans/addon/pak128.japan/Other1">Other1</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://wikiwiki.jp/twitrans?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://wikiwiki.jp/twitrans/addon/pak64/train1',
            'https://wikiwiki.jp/twitrans/addon/pak128/train1',
            'https://wikiwiki.jp/twitrans/addon/pak128.japan/Other1',
        ], $urls->values()->all());
    }

    public function test_excludes_companyindex_pages_as_duplicate_republishing(): void
    {
        Http::preventStrayRequests();

        // companyIndex は各アドオンページの内容を会社別に再編集しただけの
        // 重複ページ（実データで中身を確認済み）。prefix には一致するが
        // "index" を含むため不要ページ判定で除外される。
        $listHtml = '<html><body><div id="content"><ul>'
            .'<li><a href="/twitrans/addon/pak128/companyIndex/Train/JNR1">JNR1</a></li>'
            .'<li><a href="/twitrans/addon/pak64/companyIndex/Train/Private_Chubu1">Private_Chubu1</a></li>'
            .'<li><a href="/twitrans/addon/pak128/train1">train1</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://wikiwiki.jp/twitrans?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://wikiwiki.jp/twitrans/addon/pak128/train1',
        ], $urls->values()->all());
    }

    public function test_excludes_campany_other_series_and_notice_namespaces(): void
    {
        Http::preventStrayRequests();

        // Campany/, Other/, series/, Notice/ はいずれも addon/pak64|128|128.japan/
        // のどれにも一致しないため prefix 判定の時点で除外される。実データで
        // 中身を確認済み: Campany/Other は既存アドオンページの重複再掲載、
        // series は他サイト横断の索引表、Notice は制作予定の告知板でダウン
        // ロード対象が無い。
        $listHtml = '<html><body><div id="content"><ul>'
            .'<li><a href="/twitrans/addon/Campany/Train/JNR1">JNR1</a></li>'
            .'<li><a href="/twitrans/addon/Other/train1">train1</a></li>'
            .'<li><a href="/twitrans/addon/series/JNR">JNR</a></li>'
            .'<li><a href="/twitrans/addon/Notice/JR_JNR_1">JR_JNR_1</a></li>'
            .'<li><a href="/twitrans/addon/pak128/train1">train1</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://wikiwiki.jp/twitrans?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://wikiwiki.jp/twitrans/addon/pak128/train1',
        ], $urls->values()->all());
    }

    public function test_excludes_test_menubar_and_duplicate_pages(): void
    {
        Http::preventStrayRequests();

        $listHtml = '<html><body><div id="content"><ul>'
            .'<li><a href="/twitrans/addon/pak64/test">test</a></li>'
            .'<li><a href="/twitrans/addon/pak64/MenuBar">MenuBar</a></li>'
            .'<li><a href="/twitrans/addon/pak64/train30/%E8%A4%87%E8%A3%BD">複製</a></li>'
            .'<li><a href="/twitrans/addon/pak64/train1">train1</a></li>'
            .'</ul></div></body></html>';

        Http::fake([
            'https://wikiwiki.jp/twitrans?cmd=list' => Http::response($listHtml, 200),
        ]);

        $findUrls = new FindUrls(new FetchHtml(retryTimes: 1, sleepMilliseconds: 1, useCache: false));

        $urls = ($findUrls)();

        $this->assertSame([
            'https://wikiwiki.jp/twitrans/addon/pak64/train1',
        ], $urls->values()->all());
    }
}
