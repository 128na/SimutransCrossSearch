<?php

namespace App\Http\Controllers;

use App\Http\Requests\Articles\SearchRequest;
use App\Services\ArticleSearchService;

class ArticleController extends Controller
{
    private ArticleSearchService $search_service;

    public function __construct(ArticleSearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    public function index(SearchRequest $request)
    {
        $media_types = $request->media_types ?? [];
        $articles = $this->search_service->latest($media_types);
        return view('articles.index', compact('articles', 'media_types'));
    }
}
