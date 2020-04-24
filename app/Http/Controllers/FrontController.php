<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\SearchService;

class FrontController extends Controller
{
    /**
     * @var SearchService
     */
    private $service;

    public function __construct(SearchService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $pages = $this->service->latest();
        return view('index', compact('pages'));
    }

    public function search(SearchRequest $request)
    {
        $word = $request->word ?? '';
        $type = $request->type ?? 'and';
        $paks = $request->paks ?? [];

        $pages = $this->service->search($word, $type, $paks);
        $title = $this->service->getTitle($word, $paks);
        $canonical_url = $request->fullUrl();

        if ($pages->total()) {
            $query = str_replace([$request->url(), '?'], '', $pages->withQueryString()->url(1));
            $this->service->putSearchLog($query);
        }

        return view('search', compact('pages', 'word', 'type', 'paks', 'title', 'canonical_url'));
    }
}
