<?php

namespace Tests\Feature\Api\v1\Pages\Search;

use App\Models\Page;
use App\Models\RawPage;
use App\Models\SearchLog;
use Tests\TestCase;

class SearchLogTest extends TestCase
{
    /**
     * 検索結果がない場合、検索履歴が保存されないこと
     */
    public function testNoResultSearchLog()
    {
        $this->assertEquals(Page::count(), 0);
        $url = route('api.v1.search');

        $res = $this->get($url);
        $res->assertStatus(200);
        $this->assertEquals(SearchLog::count(), 0);
    }

    /**
     * 検索結果がある場合、検索履歴が保存されること
     */
    public function testHasResultSearchLog()
    {
        $raw_page = RawPage::create(['site_name' => 'test site', 'url' => 'http://example.com', 'html' => 'test html']);
        $page = $raw_page->page()->create(['site_name' => 'test site', 'url' => 'http://example.com', 'title' => 'test title', 'text' => 'test text']);
        $this->assertEquals(Page::count(), 1);
        $url = route('api.v1.search');

        $res = $this->get($url);
        $res->assertStatus(200);
        $this->assertEquals(SearchLog::count(), 1);
    }
}
