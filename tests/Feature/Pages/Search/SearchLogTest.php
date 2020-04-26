<?php

namespace Tests\Feature\Pages\Search;

use App\Events\ContentsUpdated;
use App\Models\Page;
use App\Models\RawPage;
use App\Models\SearchLog;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SearchLogTest extends TestCase
{
    /**
     * 検索結果がない場合、検索履歴が保存されないこと
     */
    public function testNoResultSearchLog()
    {
        Event::fake();

        $this->assertEquals(Page::count(), 0);
        $url = route('pages.search');
        Event::assertNotDispatched(ContentsUpdated::class);

        $res = $this->get($url);
        $res->assertStatus(200);
        $this->assertEquals(SearchLog::count(), 0);
        Event::assertNotDispatched(ContentsUpdated::class);
    }

    /**
     * 検索結果がある場合、検索履歴が保存されること
     */
    public function testHasResultSearchLog()
    {
        Event::fake();

        $raw_page = RawPage::create(['site_name' => 'test site', 'url' => 'http://example.com', 'html' => 'test html']);
        $page = $raw_page->page()->create(['site_name' => 'test site', 'url' => 'http://example.com', 'title' => 'test title', 'text' => 'test text']);
        $this->assertEquals(Page::count(), 1);
        $url = route('pages.search');
        Event::assertNotDispatched(ContentsUpdated::class);

        $res = $this->get($url);
        $res->assertStatus(200);
        $this->assertEquals(SearchLog::count(), 1);
        Event::assertDispatched(ContentsUpdated::class);
    }
}
