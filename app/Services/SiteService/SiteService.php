<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\RawPage;
use Goutte\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

abstract class SiteService
{
    /**
     * @var String
     */
    protected $name;

    /**
     * @var String
     */
    protected $url;

    /**
     * @var RawPage
     */
    protected $raw_page;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Pak
     */
    protected $pak;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(array $config, RawPage $raw_page, Page $page, Pak $pak)
    {
        $this->name = $config['name'];
        $this->url = $config['url'];
        $this->raw_page = $raw_page;
        $this->page = $page;
        $this->pak = $pak;
        $this->client = new Client(HttpClient::create(['timeout' => 60]));
    }

    abstract public function getUrls(): Collection;
    abstract public function extractContents(RawPage $raw_page): array;

    public function getHTML(string $url): string
    {
        $crawler = $this->client->request('GET', $url);
        return $crawler->outerHtml();
    }

    public function saveOrUpdateRawPage(string $url, string $html): RawPage
    {
        return $this->raw_page->updateOrCreate(
            ['url' => $url],
            ['site_name' => $this->name, 'html' => $html]
        );
    }

    public function removeExcludes(Collection $urls): int
    {
        return $this->raw_page
            ->where('site_name', $this->name)
            ->whereNotIn('url', $urls->toArray())
            ->delete();
    }

    public function getUpdatedRawPages(): LazyCollection
    {
        return $this->raw_page
            ->where('site_name', $this->name)
            ->whereDate('updated_at', now())
            ->cursor();
    }

    public function saveOrUpdatePage(RawPage $raw_page, array $data): Page
    {
        $page = $raw_page->page()->updateOrCreate([],
            ['site_name' => $this->name, 'url' => $raw_page->url, 'title' => $data['title'], 'text' => $data['text']]
        );

        $pak_ids = $this->pak->whereIn('slug', $data['paks'])->get()
            ->pluck('id')->unique()->toArray();
        $page->paks()->sync($pak_ids);

        return $page;
    }

    protected function getCrawler(RawPage $raw_page): Crawler
    {
        $crawler = new Crawler;
        // charasetによらず保存時にUTF8となっているため注意
        $crawler->addHtmlContent($raw_page->html, 'UTF-8');
        return $crawler;
    }
}
