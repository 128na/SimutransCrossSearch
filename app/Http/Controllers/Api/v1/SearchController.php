<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ContentsUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\SearchRequest;
use App\Http\Resources\Pages;
use App\Services\PageSearchService;
use App\Services\SearchLogService;

class SearchController extends Controller
{
    /**
     * @var PageSearchService
     */
    private $search_service;
    /**
     * @var SearchLogService
     */
    private $search_log_service;

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

        $pages = $this->search_service->search($word, $type, $paks);

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->search_log_service->put($query);
            event(new ContentsUpdated);
        }

        return new Pages($pages);
    }
}
