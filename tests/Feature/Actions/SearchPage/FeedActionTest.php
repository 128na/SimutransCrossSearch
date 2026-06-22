<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\SearchPage;

use App\Actions\SearchPage\FeedAction;
use App\Enums\SiteName;
use App\Models\Page;
use Tests\Feature\TestCase;

/**
 * D1: Feed 経路でも raw_pages（生 HTML）を露出しない。
 */
final class FeedActionTest extends TestCase
{
    public function test_get_does_not_eager_load_raw_page_relation(): void
    {
        Page::factory()->create([
            'site_name' => SiteName::Japan,
            'title' => 'Addon',
            'text' => 'body',
        ]);

        $pages = (new FeedAction)->get();

        $this->assertCount(1, $pages);
        $this->assertFalse($pages->first()?->relationLoaded('rawPage'));
    }
}
