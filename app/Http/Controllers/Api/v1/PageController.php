<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\SearchRequest;
use App\Http\Resources\Pages;
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

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';
        $type = $request->type ?? 'and';
        $paks = $request->paks ?? [];
        $search_condition = $this->search_service->parseSearchCondition($word, $type);
        $pages = $this->search_service->search($search_condition, $paks);

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->search_log_service->put($query);
        }

        return new Pages($pages);
    }
}
