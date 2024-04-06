<?php

declare(strict_types=1);

namespace App\Actions\Extract\Portal;

use App\Models\Portal\Article;

final class FindArticle
{
    public function __invoke(string $id): Article
    {
        return Article::query()
            ->with(['categories', 'tags'])
            ->findOrFail($id);
    }
}
