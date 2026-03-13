<?php

declare(strict_types=1);

namespace App\Actions\Scrape\Portal;

use App\Models\Portal\Article;
use Illuminate\Support\Collection;

final class AllUrl
{
    private const string TOP_URL = 'https://simutrans-portal.128-bit.net';

    /**
     * @return Collection<int,lowercase-string&non-falsy-string>
     */
    public function __invoke(): Collection
    {
        return Article::select('id')
            ->get()
            ->map(fn (Article $article): string => sprintf('%s/articles/%s', self::TOP_URL, $article->id));
    }
}
