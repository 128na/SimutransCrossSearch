<?php

namespace App\Services\SiteService;

use App\Models\Page;
use App\Models\Pak;
use App\Models\Portal\Article;
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
        return Article::select('slug')->get()->map(function ($article) {
            return "{$this->url}/articles/{$article->slug}";
        });
    }

    public function isUpdated(RawPage $raw_page, string $html): bool
    {
        $slug = basename($raw_page->url);
        $article = Article::select('updated_at')->where('slug', $slug)->first();

        return $article->updated_at >= $raw_page->updated_at;
    }

    public function extractContents(RawPage $raw_page): array
    {
        $slug = basename($raw_page->url);
        $article = Article::where('slug', $slug)
            ->with('categories')->first();

        $title = $article->title;
        $text = $article->text_contents;
        $paks = $article->category_paks->pluck('slug')->all();
        $last_modified = $article->updated_at;

        return compact('title', 'text', 'paks', 'last_modified');
    }

    public function getHTML(string $url): string
    {
        // 専用APIでコンテンツを取得するため不要
        return '';
    }
}
