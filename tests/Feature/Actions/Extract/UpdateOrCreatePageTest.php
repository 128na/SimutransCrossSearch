<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract;

use App\Actions\Extract\UpdateOrCreatePage;
use App\Models\Page;
use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Tests\Feature\TestCase;

/**
 * A3: 同一 RawPage に対して extract を再実行しても Page が重複しない（冪等）。
 */
final class UpdateOrCreatePageTest extends TestCase
{
    public function test_running_twice_keeps_a_single_page(): void
    {
        $rawPage = RawPage::factory()->create();
        $updateOrCreatePage = new UpdateOrCreatePage;

        $page = $updateOrCreatePage($rawPage, 'タイトル', '本文', CarbonImmutable::now());
        $second = $updateOrCreatePage($rawPage->fresh(), 'タイトル更新', '本文更新', CarbonImmutable::now());

        // 行は 1 件のまま、同一 Page が更新される。
        $this->assertSame(1, Page::query()->where('raw_page_id', $rawPage->id)->count());
        $this->assertSame($page->id, $second->id);
        $this->assertSame('タイトル更新', $second->fresh()?->title);
    }
}
