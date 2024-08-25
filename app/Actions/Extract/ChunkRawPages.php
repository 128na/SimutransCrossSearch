<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Enums\SiteName;
use App\Models\RawPage;
use Illuminate\Support\Collection;

final class ChunkRawPages
{
    /**
     * @param  callable(Collection<int, RawPage>): mixed  $fn
     */
    public function __invoke(SiteName $siteName, callable $fn): void
    {
        RawPage::query()
            ->where('site_name', $siteName)
            ->with('page')
            ->chunkById(100, $fn);
    }
}
