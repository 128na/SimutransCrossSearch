<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;

class SitemapController extends Controller
{
    /**
     * @var SitemapService
     */
    private $service;

    public function __construct(SitemapService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $sitemap = $this->service->getOrGenerate();

        return response($sitemap)->header('Content-Type', 'text/xml');
    }
}
