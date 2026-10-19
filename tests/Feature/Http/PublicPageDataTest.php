<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Actions\SearchPage\SearchAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Tests\Feature\TestCase;

final class PublicPageDataTest extends TestCase
{
    public function test_search_does_not_eager_load_raw_page_relation(): void
    {
        $pak = Pak::factory()->create(['slug' => PakSlug::Pak128]);
        $page = Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Addon',
            'text' => 'body',
        ]);
        $page->paks()->attach($pak);

        $result = (new SearchAction)([
            'keyword' => '',
            'paks' => [PakSlug::Pak128->value],
            'sites' => [SiteName::Japan->value],
        ]);

        $this->assertCount(1, $result->items());
        // 生 HTML を持つ rawPage リレーションがロードされていないこと。
        $this->assertFalse($result->items()[0]->relationLoaded('rawPage'));
    }

    /**
     * RSS Feed（Page::toFeedItem）も内部フィールド・生 HTML を含めないこと。
     */
    public function test_feed_item_does_not_expose_internal_fields_or_raw_html(): void
    {
        $rawPage = RawPage::factory()->create(['html' => '<html>secret-internal-html</html>']);
        $page = Page::factory()->create([
            'raw_page_id' => $rawPage->id,
            'site_name' => SiteName::Japan,
            'title' => 'Addon Title',
            'text' => 'Addon body text',
        ]);

        $feedItem = $page->toFeedItem();
        $encoded = (string) json_encode($feedItem);

        $this->assertStringNotContainsString('secret-internal-html', $encoded);
    }
}
