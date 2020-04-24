<?php

namespace Tests\Feature\Command;

use App\Services\SiteService\JapaneseSimutransSiteService as SiteService;
use Tests\TestCases\ScrapeTestCase;

class ScrapeJapaneseSimutransTest extends ScrapeTestCase
{
    protected $site_service_class = SiteService::class;

    public function testScrape()
    {
        $command = 'page:scrape japan';

        $this->assertDatabaseMissing('raw_pages', ['url' => 'http://example.com']);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('raw_pages', ['url' => 'http://example.com', 'html' => 'first example']);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('raw_pages', ['url' => 'http://example.com', 'html' => 'second example']);
    }
}
