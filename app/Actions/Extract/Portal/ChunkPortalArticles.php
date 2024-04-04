<?php

declare(strict_types=1);

namespace App\Actions\Extract\Portal;

use App\Models\Portal\Article;
use Closure;

class ChunkPortalArticles
{
    /**
     * @param  Closure(\Illuminate\Support\Collection<int,\App\Models\Portal\Article>):void  $fn
     */
    public function __invoke(Closure $fn): void
    {
        Article::query()->with('categories')->chunkById(100, $fn);
    }
}
