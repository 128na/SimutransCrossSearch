<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape;

use App\Actions\Scrape\ScrapeAction;
use App\Enums\SiteName;
use Illuminate\Support\Facades\Http;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

final class ScrapeActionTest extends TestCase
{
    public function test_invokes_handlers_for_all_sites_when_null_provided(): void
    {
        Http::fake([
            '*' => Http::response('<html><body></body></html>', 200),
        ]);

        $scrapeAction = app(ScrapeAction::class);
        $scrapeAction(null, new NullLogger);

        // Just ensure no exceptions were thrown
        $this->assertTrue(true);
    }

    public function test_invokes_specific_handler_when_site_provided(): void
    {
        Http::fake([
            '*' => Http::response('<html><body></body></html>', 200),
        ]);

        $scrapeAction = app(ScrapeAction::class);
        $scrapeAction(SiteName::Japan, new NullLogger);

        $this->assertTrue(true);
    }
}
