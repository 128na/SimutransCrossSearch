<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Extract\Twitrans;

use App\Actions\Extract\Twitrans\ExtractLastModified;
use App\Models\RawPage;
use Carbon\CarbonImmutable;
use Tests\Feature\TestCase;

final class ExtractLastModifiedTest extends TestCase
{
    public function test_extracts_last_modified_date(): void
    {
        $rawPage = RawPage::factory()->make([
            'html' => '<html><body><div id="lastmodified">Last-modified: 2023-11-20 10:00:00 (月)</div></body></html>',
        ]);

        $action = new ExtractLastModified;
        $date = $action($rawPage);

        $this->assertInstanceOf(CarbonImmutable::class, $date);
        $this->assertSame('2023-11-20 10:00:00', $date->format('Y-m-d H:i:s'));
    }
}
