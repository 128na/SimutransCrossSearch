<?php

namespace App\Services\SiteService;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseSiteService
{
    /**
     * @var String
     */
    protected $name;
    /**
     * @var String
     */
    protected $display_name;
    /**
     * @var String
     */
    protected $url;
    /**
     * @var Page
     */
    protected $page;

    public function __construct(array $config, Page $page)
    {
        $this->name = $config['name'];
        $this->display_name = $config['display_name'];
        $this->url = $config['url'];
        $this->page = $page;
    }

    public function getUrls(): Collection
    {}
    public function getHTML(string $url): string
    {}
    public function saveOrUpdate(string $url, string $html): Page
    {}
    public function removeExcludes(Collection $urls): int
    {}
    public function getUpdatedPages(): Page
    {}
    public function extractContents(Page $page): array
    {}
    public function updatePage(Page $page, array $data): Page
    {}
}
