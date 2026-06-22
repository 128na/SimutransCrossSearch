<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract\Japan;

use App\Actions\Extract\ChunkRawPages;
use App\Actions\Extract\Japan\ExtractContents;
use App\Actions\Extract\Japan\ExtractLastModified;
use App\Actions\Extract\Japan\Handler;
use App\Actions\Extract\MarkExtractFailed;
use App\Actions\Extract\SyncPak;
use App\Actions\Extract\UpdateOrCreatePage;
use App\Enums\SiteName;
use App\Models\RawPage;
use Psr\Log\NullLogger;
use Tests\Feature\TestCase;

/**
 * A1: 1 件の抽出失敗で他の RawPage 処理が止まらないこと（フォールト分離）。
 * A4: 失敗した RawPage は削除されず失敗フラグが立つこと。
 */
final class HandlerIsolationTest extends TestCase
{
    public function test_failure_on_one_raw_page_does_not_stop_the_others(): void
    {
        // div#lastmodified が無いため ExtractLastModified が自然に失敗する。
        $rawPageA = RawPage::factory()->create([
            'site_name' => SiteName::Japan,
            'url' => 'https://japanese.simutrans.com/index.php?Addon128%2FBroken',
            'html' => '<html><body><div id="body">no lastmodified here</div></body></html>',
        ]);

        // 正常に抽出できる HTML。
        $rawPageB = RawPage::factory()->create([
            'site_name' => SiteName::Japan,
            'url' => 'https://japanese.simutrans.com/index.php?Addon128%2FOk',
            'html' => '<html><head><title>OK Addon - Simutrans日本語化･解説</title></head>'
                .'<body><div id="body">本文</div><div id="lastmodified">Last-modified: 2024-01-01 00:00:00 (月)</div></body></html>',
        ]);

        $handler = new Handler(
            new ChunkRawPages,
            new ExtractLastModified,
            new ExtractContents,
            new UpdateOrCreatePage,
            new SyncPak,
            new MarkExtractFailed,
        );

        $handler(new NullLogger);

        // b は失敗した a の後でも処理され、Page が作られる。
        $this->assertDatabaseHas('pages', ['raw_page_id' => $rawPageB->id]);
        $this->assertDatabaseMissing('pages', ['raw_page_id' => $rawPageA->id]);

        // a は削除されず、失敗フラグが立つ。b はフラグなし。
        $this->assertDatabaseHas('raw_pages', ['id' => $rawPageA->id]);
        $this->assertNotNull($rawPageA->fresh()?->extract_failed_at);
        $this->assertNull($rawPageB->fresh()?->extract_failed_at);
    }
}
