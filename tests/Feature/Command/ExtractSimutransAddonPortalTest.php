<?php

namespace Tests\Feature\Command;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use App\Services\SiteService\SimutransAddonPortalSiteService as SiteService;
use Mockery;
use Tests\TestCases\ExtractTestCase;

class ExtractSimutransAddonPortalTest extends ExtractTestCase
{
    protected $site_service_class = SiteService::class;

    protected function setUp(): void
    {
        parent::setUp();

        // 抽出処理をモックする
        $this->instance($this->site_service_class,
            Mockery::mock($this->site_service_class, [app(RawPage::class), app(Page::class), app(Pak::class)], function ($mock) {
                $mock->shouldReceive('extractContents')->times(2)->andReturn(
                    ['title' => 'first title', 'text' => 'first text', 'paks' => ['64', '128']],
                    ['title' => 'second title', 'text' => 'second text', 'paks' => ['128', '128-japan']]
                );
            })->makePartial()
        );
    }

    public function testScrape()
    {
        $command = 'page:extract portal';

        $raw_page = RawPage::create(['site_name' => 'portal', 'html' => '', 'url' => 'http://example.com']);

        $this->assertDatabaseMissing('pages', ['url' => 'http://example.com']);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('pages', ['url' => 'http://example.com', 'title' => 'first title', 'text' => 'first text']);
        $page = Page::first();
        $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak64->id]);
        $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128->id]);
        $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128jp->id]);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('pages', ['url' => 'http://example.com', 'title' => 'second title', 'text' => 'second text']);
        $this->assertDatabaseMissing('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak64->id]);
        $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128->id]);
        $this->assertDatabaseHas('page_pak', ['page_id' => $page->id, 'pak_id' => $this->pak128jp->id]);
    }
}
