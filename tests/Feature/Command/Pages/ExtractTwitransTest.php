<?php

namespace Tests\Feature\Command\Pages;

use App\Models\Page;
use App\Models\RawPage;
use Tests\MockHTML;
use Tests\TestCases\ExtractTestCase;

class ExtractTwitransTest extends ExtractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExtract64()
    {
        $this->extract('https://wikiwiki.jp/twitrans/addon/pak64/building1',
            function (Page $page) {
                $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak64->id]);
                $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128->id]);
                $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128jp->id]);
            });
    }

    public function testExtract128()
    {
        $this->extract('https://wikiwiki.jp/twitrans/addon/pak128/building1',
            function (Page $page) {
                $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak64->id]);
                $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128->id]);
                $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128jp->id]);
            });
    }

    public function testExtract128jp()
    {
        $this->extract('https://wikiwiki.jp/twitrans/addon/pak128.japan/building1',
            function (Page $page) {
                $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak64->id]);
                $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128->id]);
                $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128jp->id]);
            });
    }

    private function extract($url, \Closure $assert_fn)
    {
        $command = 'page:extract twitrans';

        $html = MockHTML::twitrans('first title', 'first text', 'exclude text');
        $raw_page = RawPage::create(['site_name' => 'twitrans', 'html' => $html, 'url' => $url]);

        $this->assertDatabaseMissing('pages', ['url' => $url]);
        $this->assertDatabaseMissing('page_pak', ['pak_id' => $this->pak64->id]);
        $this->assertDatabaseMissing('page_pak', ['pak_id' => $this->pak128->id]);
        $this->assertDatabaseMissing('page_pak', ['pak_id' => $this->pak128jp->id]);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('pages', ['url' => $url, 'title' => 'first title', 'text' => 'first text']);
        $page = Page::first();
        $assert_fn($page);

        $html = MockHTML::twitrans('second title', 'second text', 'exclude text');
        $raw_page->update(['html' => $html]);
        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('pages', ['url' => $url, 'title' => 'second title', 'text' => 'second text']);
        $assert_fn($page);
    }
}
