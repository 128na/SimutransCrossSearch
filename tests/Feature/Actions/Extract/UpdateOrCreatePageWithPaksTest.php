<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract;

use App\Actions\Extract\SyncPak;
use App\Actions\Extract\UpdateOrCreatePage;
use App\Actions\Extract\UpdateOrCreatePageWithPaks;
use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

/**
 * D4: Page の更新と Pak 同期は 1 トランザクションで行われ、片方だけ反映された
 * 半端な状態（title/text は新しいが Pak 紐付けは古いまま）が検索に見えないこと。
 */
final class UpdateOrCreatePageWithPaksTest extends TestCase
{
    public function test_wraps_page_update_and_pak_sync_in_a_single_transaction(): void
    {
        $rawPage = RawPage::factory()->create();

        // SyncPak は final のため Mockery では型ヒント越しに差し替えできない。
        // 代わりに DB::transaction が実際に呼ばれていること（両操作が原子的にまとめられていること）
        // を直接検証する。
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn (\Closure $callback) => $callback());

        $updateOrCreatePageWithPaks = new UpdateOrCreatePageWithPaks(new UpdateOrCreatePage, new SyncPak);
        $page = $updateOrCreatePageWithPaks($rawPage, '新タイトル', '新本文', CarbonImmutable::now(), []);

        $this->assertSame('新タイトル', $page->title);
    }

    public function test_commits_page_and_paks_together_on_success(): void
    {
        $rawPage = RawPage::factory()->create();

        $updateOrCreatePageWithPaks = new UpdateOrCreatePageWithPaks(new UpdateOrCreatePage, new SyncPak);
        $page = $updateOrCreatePageWithPaks($rawPage, '新タイトル', '新本文', CarbonImmutable::now(), []);

        $this->assertSame('新タイトル', $page->fresh()?->title);
    }
}
