<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Pagination\Paginator;

class ArticleSearchService
{
    private Article $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function latest($media_types = [], $limit = 50): Paginator
    {
        $query = $this->model
            ->select('id', 'site_name', 'media_type', 'title', 'url', 'thumbnail_url', 'last_modified');

        if (count($media_types)) {
            $query->whereIn('media_type', $media_types);
        }

        return $query->orderBy('last_modified', 'desc')->simplePaginate($limit);
    }
}
