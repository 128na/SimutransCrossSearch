<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract\Japan;

use App\Actions\Extract\Japan\ExtractContents;
use App\Enums\PakSlug;
use App\Models\RawPage;
use Tests\Feature\TestCase;

final class ExtractContentsTest extends TestCase
{
    public function test_extracts_title_text_and_pak(): void
    {
        $rawPage = RawPage::factory()->make([
            'url' => 'https://japanese.simutrans.com/index.php?addon128%2fTest',
            'html' => '<html><head><title>Awesome Addon - Simutrans日本語化･解説</title></head><body><div id="body">This is an awesome addon description.</div></body></html>',
        ]);

        $extractContents = new ExtractContents;
        $result = $extractContents($rawPage);

        $this->assertSame('Awesome Addon', $result['title']);
        $this->assertSame('This is an awesome addon description.', $result['text']);
        $this->assertEquals([PakSlug::Pak128], $result['paks']);
    }
}
