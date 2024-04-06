<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Enums\SiteName;
use App\Models\RawPage;
use Closure;

final class ChunkRawPages
{
    /**
     * @param  Closure(\Illuminate\Database\Eloquent\Collection<int,\App\Models\RawPage>):void  $fn
     */
    public function __invoke(SiteName $siteName, Closure $fn): void
    {
        RawPage::query()
            ->where('site_name', $siteName)
            ->with('page')
            ->chunkById(100, $fn);
    }
}
