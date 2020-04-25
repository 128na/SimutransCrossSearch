<?php
namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleSearchService
{
    /**
     * @var Article
     */
    private $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function latest(): Collection
    {
        return $this->model
            ->select('id', 'site_name', 'media_type', 'title', 'url', 'thumbnail_url', 'last_modified')
            ->whereDate('last_modified', '>', now()->modify('-3 months'))
            ->orderBy('last_modified', 'desc')
            ->get();
    }
}
