<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract\Japan;

use App\Actions\Extract\Japan\ExtractLastModified;
use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Tests\Feature\TestCase;

final class ExtractLastModifiedTest extends TestCase
{
    public function test_extracts_last_modified_date(): void
    {
        $rawPage = RawPage::factory()->make([
            'html' => '<html><body><div id="lastmodified">Last-modified: 2023-05-15 14:30:00 (月)</div></body></html>',
        ]);

        $extractLastModified = new ExtractLastModified;
        $date = $extractLastModified($rawPage);

        $this->assertInstanceOf(CarbonImmutable::class, $date);
        $this->assertSame('2023-05-15 14:30:00', $date->format('Y-m-d H:i:s'));
    }
}
