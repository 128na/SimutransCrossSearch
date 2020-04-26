<?php
namespace App\Listeners;

use App\Events\ContentsUpdated;
use App\Services\SitemapService;

class UpdateSitemap
{
    /**
     * @var SitemapService
     */
    private $service;

    public function __construct(SitemapService $service)
    {
        $this->service = $service;
    }

    /**
     * イベントの処理
     *
     * @return void
     */
    public function handle(ContentsUpdated $event)
    {
        $this->service->generate();
    }
}
