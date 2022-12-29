<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pages\SearchRequest;
use App\Services\PageSearchService;
use App\Services\SearchLogService;

class PageController extends Controller
{
    private PageSearchService $search_service;

    private SearchLogService $search_log_service;

    public function __construct(PageSearchService $search_service, SearchLogService $search_log_service)
    {
        $this->search_service = $search_service;
        $this->search_log_service = $search_log_service;
    }

    public function index()
    {
        $pages = $this->search_service->latest();

        return view('pages.index', compact('pages'));
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';
        $type = $request->type ?? 'and';
        $paks = $request->paks ?? [];

        $search_condition = $this->search_service->parseSearchCondition($word, $type);
        $pages = $this->search_service->search($search_condition, $paks);
        $title = $this->search_service->getTitle($word, $paks);
        $canonical_url = $request->fullUrl();

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->search_log_service->put($query);
        }

        return view('pages.search', compact('pages', 'word', 'type', 'paks', 'title', 'canonical_url', 'search_condition'));
    }
}
