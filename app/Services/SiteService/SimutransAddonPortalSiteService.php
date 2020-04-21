<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Illuminate\Support\Collection;

class SimutransAddonPortalSiteService extends SiteService
{
    private $token;

    public function __construct(RawPage $raw_page, Page $page, Pak $pak)
    {
        parent::__construct(
            config('sites.portal'),
            $raw_page,
            $page,
            $pak
        );
        $this->token = config('sites.portal.token');
    }

    public function getUrls(): Collection
    {
        $url = $this->url . "/api/v2/cross-search?token={$this->token}";
        $crawler = $this->client->request('GET', $url);
        $json = $this->client->getResponse()->getContent();

        $urls = json_decode($json, true);
        return collect($urls);
    }

    public function extractContents(RawPage $raw_page): array
    {
        $slug = basename($raw_page->url);
        $url = $this->url . "/api/v2/cross-search/{$slug}?token={$this->token}";
        $crawler = $this->client->request('GET', $url);
        $json = $this->client->getResponse()->getContent();

        $data = json_decode($json, true);

        $title = $data['data']['title'];
        $text = $data['data']['contents'];
        $paks = $data['data']['paks'];

        return compact('title', 'text', 'paks');
    }

    public function getHTML(string $url): string
    {
        // 専用APIでコンテンツを取得するため不要
        return '';
    }
}
