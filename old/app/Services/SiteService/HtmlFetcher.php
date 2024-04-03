<?php

namespace App\Services\SiteService;

use Exception;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Response;

class HtmlFetcher extends AbstractBrowser
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  \Symfony\Component\BrowserKit\Request  $request
     */
    protected function doRequest($request): Response
    {
        return new Response($this->fetch($request->getUri()));
    }

    private function fetch(string $url): HttpResponse
    {
        $response = Http::retry(3, 1000)->get($url);
        if ($status = $response->status() !== 200) {
            throw new Exception("$url returns status: $status", 1);
        }

        return $response;
    }
}
