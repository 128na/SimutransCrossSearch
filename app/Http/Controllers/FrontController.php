<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\SearchService;

class FrontController extends Controller
{
    /**
     * @var SearchService
     */
    private $search_service;

    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    public function index()
    {
        $pages = $this->search_service->latest();
        return view('index', compact('pages'));
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';
        $type = $request->type ?? 'and';
        $paks = $request->paks ?? [];

        $pages = $this->search_service->search($word, $type, $paks);
        $title = $this->search_service->getTitle($word, $paks);

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->search_service->putSearchLog($query);
        }

        return view('search', compact('pages', 'word', 'type', 'paks', 'title'));
    }
}
