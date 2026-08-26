<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract;

use App\Actions\Extract\SyncPak;
use App\Actions\Extract\UpdateOrCreatePage;
use App\Actions\Extract\UpdateOrCreatePageWithPaks;
use App\Enums\PakSlug;
use App\Models\Page;
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
        // を直接検証する。partialMock を使い、transaction 以外の実 DB 操作（UpdateOrCreatePage/SyncPak
        // 内部のクエリ）はそのまま実行されるようにする（shouldReceive だと DB 全体が差し替わり、
        // それらのクエリが解決できなくなって壊れる）。
        DB::partialMock()
            ->shouldReceive('transaction')
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

    public function test_page_update_is_rolled_back_when_pak_sync_fails(): void
    {
        $rawPage = RawPage::factory()->create();
        $existingPage = Page::factory()->for($rawPage)->create(['title' => '旧タイトル']);

        // SyncPak / UpdateOrCreatePage は final のため、上のテストのコメント同様
        // Mockery で差し替えたフェイクを注入することができない。
        // そこで実際の SyncPak::resolvePakIds() に契約（array<int,PakSlug>）に反する
        // 値を渡し、実コード内で本物のエラー（$pak->value の読み取り失敗。PHP 8 では
        // ErrorException として捕捉される）を発生させる。これにより DB::transaction() の
        // ロールバック（D4: 原子性）を、モックではなく本物のトランザクション・
        // 本物の例外経路で検証できる。
        /** @var array<int,PakSlug> $invalidPaks */
        $invalidPaks = ['not-a-pak-slug'];

        $updateOrCreatePageWithPaks = new UpdateOrCreatePageWithPaks(new UpdateOrCreatePage, new SyncPak);

        $thrown = null;
        try {
            $updateOrCreatePageWithPaks($rawPage, '新タイトル', '新本文', CarbonImmutable::now(), $invalidPaks);
        } catch (\Throwable $e) {
            $thrown = $e;
        }

        $this->assertNotNull($thrown, 'SyncPak 内で例外が発生するはずだった');

        // Page の title 更新は SyncPak 失敗前に実行済みだが、トランザクション全体が
        // ロールバックされ、旧タイトルのままであること（rollback proven, not assumed）。
        $this->assertSame('旧タイトル', $existingPage->fresh()?->title);
    }
}
