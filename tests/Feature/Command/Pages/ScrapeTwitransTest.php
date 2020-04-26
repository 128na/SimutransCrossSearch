<?php

namespace Tests\Feature\Command\Pages;

use App\Events\ContentsUpdated;
use App\Services\SiteService\TwitransSiteService as SiteService;
use Illuminate\Support\Facades\Event;
use Tests\TestCases\ScrapeTestCase;

class ScrapeTwitransTest extends ScrapeTestCase
{
    protected $site_service_class = SiteService::class;

    public function testScrape()
    {
        $command = 'page:scrape twitrans';
        Event::fake();

        $this->assertDatabaseMissing('raw_pages', ['url' => 'http://example.com']);
        Event::assertNotDispatched(ContentsUpdated::class);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('raw_pages', ['url' => 'http://example.com', 'html' => 'first example']);
        Event::assertDispatched(ContentsUpdated::class);

        $this->artisan($command)->assertExitCode(0);
        $this->assertDatabaseHas('raw_pages', ['url' => 'http://example.com', 'html' => 'second example']);
        Event::assertDispatched(ContentsUpdated::class, 2);
    }
}
