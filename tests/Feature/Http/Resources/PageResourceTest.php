<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Resources;

use App\Actions\SearchPage\SearchAction;
use App\Enums\PakSlug;
use App\Enums\SiteName;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Illuminate\Http\Request;
use Tests\Feature\TestCase;

/**
 * D3: API/Feed 応答に内部フィールドを含めない（許可リストのみ）。
 * D1: 検索結果に raw_pages（生 HTML）を露出しない。
 */
final class PageResourceTest extends TestCase
{
    public function test_resource_exposes_only_whitelisted_fields(): void
    {
        $rawPage = RawPage::factory()->create(['html' => '<html>secret-internal-html</html>']);
        $page = Page::factory()->create([
            'raw_page_id' => $rawPage->id,
            'site_name' => SiteName::Japan,
        ]);

        $array = (new PageResource($page))->toArray(Request::create('/'));

        $this->assertSame(['title', 'site', 'paks', 'url', 'last_modified'], array_keys($array));
        $this->assertArrayNotHasKey('raw_page_id', $array);
        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('text', $array);
        $this->assertStringNotContainsString('secret-internal-html', (string) json_encode($array));
    }

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
}
