<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract;

use App\Actions\Extract\MarkExtractFailed;
use App\Models\RawPage;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

/**
 * A4: 抽出失敗時にスクレイプ済データ（RawPage）を破壊しない。
 */
final class MarkExtractFailedTest extends TestCase
{
    public function test_marks_failure_without_deleting_raw_page(): void
    {
        $rawPage = RawPage::factory()->create();

        (new MarkExtractFailed)(new NullLogger, $rawPage, new \RuntimeException('parse error'));

        // 削除されず残存していること（旧実装は delete() で消していた）。
        $this->assertDatabaseHas('raw_pages', ['id' => $rawPage->id]);
        $this->assertNotNull($rawPage->fresh()?->extract_failed_at);
    }

    public function test_clear_resets_failed_flag_on_success(): void
    {
        $rawPage = RawPage::factory()->create(['extract_failed_at' => now()]);

        (new MarkExtractFailed)->clear($rawPage);

        $this->assertNull($rawPage->fresh()?->extract_failed_at);
    }

    public function test_clear_is_noop_when_not_failed(): void
    {
        $rawPage = RawPage::factory()->create(['extract_failed_at' => null]);

        (new MarkExtractFailed)->clear($rawPage);

        $this->assertNull($rawPage->fresh()?->extract_failed_at);
    }
}
