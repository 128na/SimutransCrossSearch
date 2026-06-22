<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Page;
use App\Models\RawPage;
use Illuminate\Database\QueryException;
use Tests\Feature\TestCase;

/**
 * A5: 同一 URL の重複行を作らない（DB 一意制約が有効であること）。
 */
final class UrlUniqueConstraintTest extends TestCase
{
    public function test_raw_pages_url_is_unique(): void
    {
        RawPage::factory()->create(['url' => 'https://example.com/dup']);

        $this->expectException(QueryException::class);
        RawPage::factory()->create(['url' => 'https://example.com/dup']);
    }

    public function test_pages_url_is_unique(): void
    {
        Page::factory()->create(['url' => 'https://example.com/dup']);

        $this->expectException(QueryException::class);
        Page::factory()->create(['url' => 'https://example.com/dup']);
    }
}
