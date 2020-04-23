<?php

namespace App\Http\Controllers;

use App\Models\SearchWords;
use App\Services\PageSearchService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    /**
     * @var PageSearchService
     */
    private $page_search_service;

    public function __construct(PageSearchService $page_search_service)
    {
        $this->page_search_service = $page_search_service;
    }

    public function index()
    {
        $pages = $this->page_search_service->latest();
        return view('index', compact('pages'));
    }

    public function search(Request $request)
    {
        $word = $request->word;
        $paks = $request->paks ?? [];
        $search_words = new SearchWords($word);
        $pages = $this->page_search_service->search($search_words, $paks);
        $title = $this->page_search_service->getTitle($word, $paks);

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->page_search_service->updateSearchLog($query);
        }

        return view('search', compact('pages', 'word', 'paks', 'title'));
    }
}
