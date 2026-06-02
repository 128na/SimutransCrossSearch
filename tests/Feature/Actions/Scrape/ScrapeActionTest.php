<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Scrape;

use App\Actions\Scrape\ScrapeAction;
use App\Enums\SiteName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

final class ScrapeActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_invokes_handlers_for_all_sites_when_null_provided(): void
    {
        Http::fake([
            '*' => Http::response('<html><body></body></html>', 200),
        ]);

        $scrapeAction = app(ScrapeAction::class);
        $scrapeAction(null, new NullLogger);

        // Verify JapanHandler was invoked by checking its specific HTTP request
        Http::assertSent(fn ($request): bool => str_contains((string) $request->url(), 'japanese.simutrans.com'));
        // Verify TwitransHandler was invoked
        Http::assertSent(fn ($request): bool => str_contains((string) $request->url(), 'wikiwiki.jp/twitrans'));
    }

    public function test_invokes_specific_handler_when_site_provided(): void
    {
        Http::fake([
            '*' => Http::response('<html><body></body></html>', 200),
        ]);

        $scrapeAction = app(ScrapeAction::class);
        $scrapeAction(SiteName::Japan, new NullLogger);

        Http::assertSent(fn ($request): bool => str_contains((string) $request->url(), 'japanese.simutrans.com'));
        Http::assertNotSent(fn ($request): bool => str_contains((string) $request->url(), 'wikiwiki.jp/twitrans'));
    }
}
