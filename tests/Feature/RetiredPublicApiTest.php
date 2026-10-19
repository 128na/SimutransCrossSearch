<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;

final class RetiredPublicApiTest extends TestCase
{
    public function test_public_search_api_is_not_registered_or_accessible(): void
    {
        foreach (Route::getRoutes() as $route) {
            $this->assertFalse(str_starts_with($route->uri(), 'api/'));
        }

        $query = http_build_query(['keyword' => '', 'paks' => [128], 'sites' => ['japan']]);

        $this->get('/api/v2/search?'.$query)->assertNotFound();
        $this->getJson('/api/v2/search?'.$query)->assertNotFound();
        $this->head('/api/v2/search')->assertNotFound();
    }

    public function test_header_preserves_feed_link_and_removes_api_documentation_link(): void
    {
        $header = view('header')->render();

        $this->assertStringContainsString('href="/feed"', $header);
        $this->assertStringNotContainsString('simutrans-intro.notion.site/API-', $header);
    }
}
