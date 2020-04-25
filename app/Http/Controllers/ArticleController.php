<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\ArticleSearchService;

class ArticleController extends Controller
{
    /**
     * @var ArticleSearchService
     */
    private $search_service;

    public function __construct(ArticleSearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    public function index()
    {
        $articles = $this->search_service->latest();
        return view('articles.index', compact('articles'));
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';
        $type = $request->type ?? 'and';
        $paks = $request->paks ?? [];

        $pages = $this->search_service->search($word, $type, $paks);
        $title = $this->search_service->getTitle($word, $paks);
        $canonical_url = $request->fullUrl();

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->search_log_service->put($query);
        }

        return view('search', compact('pages', 'word', 'type', 'paks', 'title', 'canonical_url'));
    }
}
