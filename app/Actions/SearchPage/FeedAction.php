<?php

declare(strict_types=1);

namespace App\Actions\SearchPage;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

final class FeedAction
{
    /**
     * @return Collection<int,Page>
     */
    public function get(): Collection
    {
        return Page::query()
            ->with('paks')
            ->orderBy('last_modified', 'desc')
            ->limit(50)
            ->get();
    }
}
