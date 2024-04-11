<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Portal;

use App\Models\Portal\Article;
use Illuminate\Support\LazyCollection;

final class CursorUrl
{
    private const string TOP_URL = 'https://simutrans-portal.128-bit.net';

    /**
     * @return LazyCollection<int,string>
     */
    public function __invoke(): LazyCollection
    {
        return Article::select('id')
            ->cursor()
            ->map(fn (Article $article): string => sprintf('%s/articles/%s', self::TOP_URL, $article->id));
    }
}
