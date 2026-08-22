<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Twitrans;

use App\Actions\Scrape\FetchHtml;
use App\Enums\Encoding;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

final readonly class FindUrls
{
    private const string DOMAIN = 'https://wikiwiki.jp';

    private const string LIST_URL = 'https://wikiwiki.jp/twitrans?cmd=list';

    public function __construct(
        private FetchHtml $fetchHtml,
    ) {}

    /**
     * @return Collection<int,string>
     */
    public function __invoke(): Collection
    {
        return $this->getTargetUrls()
            ->filter(fn (string $url): bool => $this->filter($url))
            ->map(fn (string $url): string => $this->toFullUrl($url));
    }

    /**
     * @return Collection<int,string>
     */
    private function getTargetUrls(): Collection
    {
        $response = ($this->fetchHtml)(self::LIST_URL, Encoding::UTF_8);
        $urls = $response
            ->filter('#content>ul li')
            ->each(fn (Crawler $crawler): ?string => $crawler->filter('a')->attr('href'));

        return collect($urls)->filter(fn ($url): bool => is_string($url));
    }

    /**
     * 対象ページの判定基準（2026-08-22 網羅性調査で確認済み）。
     *
     * 【取得対象】addon/pak64/, addon/pak128/, addon/pak128.japan/ 配下の
     * アドオン本体ページ。サイトの sitemap.txt（?cmd=list とは独立した
     * 列挙経路）と突き合わせても漏れが無いことを確認済み。
     *
     * 【意図的に対象外にしているページ】以下はいずれも実データで中身を確認
     * 済みで、含めると同じアドオンが検索結果に重複して出るか、そもそも
     * ダウンロード対象が存在しないページのため、意図的に対象外のままに
     * している。
     * - addon/pak64|pak128/companyIndex/ : 各アドオンページの内容を会社別に
     *   再編集しただけの一覧（prefix には一致するが "index" を含むため
     *   下の不要ページ判定で弾かれる）
     * - addon/Campany/ , addon/Other/    : companyIndex 以前からある、内容は
     *   同じアドオンを別カテゴリで再掲載したページ（addon/pak64|128|128.japan/
     *   のいずれにも一致しないため、上の prefix 判定の時点で弾かれる）
     * - addon/series/                    : アドオン本体ではなく、実験室/日本語化
     *                                       wiki/Addon Portal 横断の索引表
     *                                       （同様に prefix 不一致で除外）
     * - addon/Notice/                    : 未公開・制作予定アドオンの告知板
     *                                       (ダウンロード対象が存在しない。
     *                                       同様に prefix 不一致で除外)
     */
    private function filter(string $url): bool
    {
        $url = strtolower($url);
        // アドオンページ以外
        if (! str_contains($url, 'addon/pak64/') && ! str_contains($url, 'addon/pak128/') && ! str_contains($url, 'addon/pak128.japan/')) {
            return false;
        }

        // 不要ページ（試験用・会社別索引ページ・共通メニュー・複製ページ）
        return ! (str_contains($url, 'test') || str_contains($url, 'index') || str_contains($url, 'menubar') || str_contains($url, '%e8%a4%87%e8%a3%bd'));
    }

    /**
     * 単純な相対→絶対URLの連結。Japan\FindUrls::toFullUrl() と異なり、
     * このサイトの href はパーセントエンコードの再構築を必要としない。
     */
    private function toFullUrl(string $url): string
    {
        return self::DOMAIN.$url;
    }
}
