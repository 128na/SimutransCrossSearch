<?php

declare(strict_types=1);

namespace App\Actions\SyncNotion;

use App\Models\Page;
use Illuminate\Support\Collection;
use Notion\Databases\Database;
use Notion\Notion;
use Notion\Pages\Page as NotionPage;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Date;
use Notion\Pages\Properties\MultiSelect;
use Notion\Pages\Properties\RichTextProperty;
use Notion\Pages\Properties\Url;

final class SyncAction
{
    private const PAGE_PROP_MAPPING = [
        'title' => 'タイトル',
        'site_name' => 'サイト名',
        'last_modified' => '最終更新日時',
        'url' => 'URL',
        'paks' => 'パックセット',
    ];

    public function __construct(private readonly Notion $notion)
    {
    }

    public function __invoke(string $databaseId, int $limit)
    {
        $database = $this->notion->databases()->find($databaseId);
        $notionPages = collect($this->notion->databases()->queryAllPages($database));

        $pages = Page::query()->with('paks')->orderBy('last_modified', 'desc')->limit($limit)->get();

        $this->deleteOldNotionPages($pages, $notionPages);
        $this->addNewNotionPages($database, $pages, $notionPages);
    }

    /**
     * @param  Collection<Page>  $pages
     * @param  Collection<NotionPage>  $notionPages
     */
    private function deleteOldNotionPages(Collection $pages, Collection $notionPages): void
    {
        foreach ($notionPages as $np) {
            $url = $np->getProperty(self::PAGE_PROP_MAPPING['url'])->url;
            $page = $pages->first(fn (Page $p) => $p->url === $url);
            if ($page) {
                // addNewNotionPages側で更新するのでスキップ
            } else {
                logger('[NotionService]delete', ['url' => $url]);
                $this->notion->pages()->delete($np);
            }
        }
    }

    /**
     * @param  Collection<Page>  $pages
     * @param  Collection<NotionPage>  $notionPages
     */
    private function addNewNotionPages(Database $database, Collection $pages, Collection $notionPages): void
    {
        /**
         * @var Collection<\Notion\Databases\Properties\SelectOption> $options
         */
        $options = collect($database->properties()->get(self::PAGE_PROP_MAPPING['paks'])->options);
        foreach ($pages as $page) {
            $url = $page->url;
            $np = $notionPages->first(fn (NotionPage $np) => $np->getProperty(self::PAGE_PROP_MAPPING['url'])->url === $url);
            if ($np) {
                logger('[NotionService]update', ['url' => $url]);
                $this->notion->pages()->update($np
                    ->changeTitle($page->title)
                    ->addProperty(self::PAGE_PROP_MAPPING['site_name'], RichTextProperty::fromString($page->displaySiteName))
                    ->addProperty(self::PAGE_PROP_MAPPING['last_modified'], Date::create($page->last_modified->toDateTimeImmutable()))
                    ->addProperty(self::PAGE_PROP_MAPPING['paks'], MultiSelect::fromOptions(
                        ...$page->paks->pluck('name')->map(fn ($name) => $options->first(fn ($opt) => $name === $opt->name))
                    ))
                );
            } else {
                logger('[NotionService]create', ['url' => $url]);
                $this->notion->pages()->create(NotionPage::create(PageParent::database($database->id))
                    ->changeTitle($page->title)
                    ->addProperty(self::PAGE_PROP_MAPPING['url'], Url::create($page->url))
                    ->addProperty(self::PAGE_PROP_MAPPING['site_name'], RichTextProperty::fromString($page->displaySiteName))
                    ->addProperty(self::PAGE_PROP_MAPPING['last_modified'], Date::create($page->last_modified->toDateTimeImmutable()))
                    ->addProperty(self::PAGE_PROP_MAPPING['paks'], MultiSelect::fromOptions(
                        ...$page->paks->pluck('name')->map(fn ($name) => $options->first(fn ($opt) => $name === $opt->name))
                    ))
                );
            }
        }
    }
}
