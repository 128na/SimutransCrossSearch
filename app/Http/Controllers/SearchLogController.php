<?php

namespace App\Http\Controllers;

use App\Services\SearchLogService;

class SearchLogController extends Controller
{
    private SearchLogService $service;

    public function __construct(SearchLogService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $logs = $this->service->getRanking(20);
        return view('logs.search', compact('logs'));
    }
}
