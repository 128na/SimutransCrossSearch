<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract\Twitrans;

use App\Actions\Extract\Twitrans\ExtractContents;
use App\Enums\PakSlug;
use App\Models\RawPage;
use Tests\Feature\TestCase;

final class ExtractContentsTest extends TestCase
{
    public function test_extracts_title_text_and_pak(): void
    {
        $rawPage = RawPage::factory()->make([
            'url' => 'https://wikiwiki.jp/twitrans/addon/pak128.japan/test',
            'html' => '<html><head><title>Twitrans Addon - Simutrans的な実験室 Wiki*</title></head><body><div id="content">This is a twitrans addon description.</div></body></html>',
        ]);

        $action = new ExtractContents;
        $result = $action($rawPage);

        $this->assertSame('Twitrans Addon', $result['title']);
        $this->assertSame('This is a twitrans addon description.', $result['text']);
        $this->assertEquals([PakSlug::Pak128Jp], $result['paks']);
    }
}
